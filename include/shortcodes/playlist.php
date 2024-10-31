<?php

// Block direct access to file
defined( 'ABSPATH' ) or die( 'Not Authorized!' );

class PRESS_TUBE_Shortcode_Playlist {

  /**
   * The shortcode activation name
   * @var string
   */
  private $shortcode_name = 'playlist';

  /**
   * The class instance
   * @var object
   */
  private static $instance;

  public function __construct() {
    add_shortcode( $this->shortcode_name, array($this, 'do_shortcode') );
  }

  public static function get_instance() {
    if ( ! ( self::$instance instanceof self ) ) {
			self::$instance = new self();
		}
		return self::$instance;
  }

  /**
   * The shortcode print function
   * @method do_shortcode
   */
  public function do_shortcode($atts, $content = null) {

    // Get the shortcode options
    $options = shortcode_atts( array(
  		'id' => null,
      'display_mode' => 'default'
  	), $atts );

    // Exit if no id is found
    if( empty($options['id']) ) {
      return;
    }

    $output = '<div class="press-tube-shortcode playlist playlist-container">';

    // Add playlist description text if specified
    if( $content ) {
      $output .= '<div class="playlist-description">' . $content . '</div>';
    }

    $output .= '<div class="playlist-content ' . $options['display_mode'] . '">';

    // If is a valid YouTube playlist id do the standard embed for now
    if( PRESS_TUBE_Utils::validate_playlist($options['id']) ) {

      $this->print_playlist_from_youtube($options, $output);

    // Otherwise check if the post has playlist items associated with it
    } else if( $playlist_items = get_post_meta( $options['id'], 'wt_playlist_items', true ) ) {

      $this->print_custom_playlist( $output, $playlist_items, $options );

    } else {

      // playlist cant be shown
      $output .= $this->print_error_warning();

    }

    // Close the playlist content container
    $output .= '</div>';

    // Close the main playlist container
    $output .= '</div>';

    return $output;

  }

  private function print_error_warning() {

    // TODO: put a link to post edit page
    if( current_user_can('edit_others_posts') ) {
      // playlist cant be shown
      return __( 'The playlist does not exist.', 'press-tube' );
    }

  }

  private function build_main_frame($url) {

    return sprintf(
      '<div class="frame-wrapper"><iframe src="%s" frameborder="0" allowfullscreen></iframe></div>', $url
    );

  }

  /**
   * Print a normal playlist embed as youtube usual
   * @method print_playlist_embed
   */
  private function print_playlist_embed(&$output, $options) {

    $url = sprintf('https://www.youtube.com/embed/?%s', http_build_query(array(
      'list' => $options['id'],
      'listType' => 'playlist'
    )));

    $output .= $this->build_main_frame($url);

  }

  /**
   * TODO: Finish this method in order to activate it!
   * Print a custom wordpress playlist in various formats
   * @method print_custom_playlist
   */
  private function print_custom_playlist(&$output, $items, $options) {

    // Build url disabling by default the relateds since are displayed artificially
    $url = sprintf( 'https://www.youtube.com/embed/%s?%s', $items[0]['id'], http_build_query(array(
      'rel' => '0'
    )) );

    $output .= '<div class="playlist-frame">';

    $output .= $this->build_main_frame($url);

    $output .= '</div>';

    $output .= '<div class="playlist-items-container">';

    foreach ($items as $playlist_item) {

      $item = sprintf(
        '<div class="playlist-item" data-video-id="%s">
          <div class="playlist-item-thumbnail"><img src="%s" title="%s" /></div>
          <div class="playlist-item-details">
            <h3 class="playlist-item-title">%s</h3>
            <p class="playlist-item-description">%s</p>
          </div>
        </div>',
        $playlist_item['id'],
        $playlist_item['thumbnail'],
        $playlist_item['title'],
        $playlist_item['title'],
        wp_trim_words($playlist_item['description'], 18)
      );

      $output .= $item;

    }

    $output .= '</div>';

  }

  /**
   * Print the playlist for a valid given YouTube playlistId
   * this function can handle the default youtube display or
   * the custom list, slider and gallery displays
   * @method print_playlist_from_youtube
   */
  private function print_playlist_from_youtube($options, &$output) {

    if( $options['display_mode'] === 'default' ) {

      // print the normal youtube playlist embed frame
      $this->print_playlist_embed( $output, $options );

    } else {

      // If playlist items were retrived from youtube
      if( $playlist_items = PRESS_TUBE_Playlist::get_playlist_items_from_youtube($options['id']) ) {

        // Do the custom playlist rendering functions that threat it
        // as it was a custom playlist creted in WordPress by user
        $this->print_custom_playlist( $output, $playlist_items, $options );

      } else {

        // playlist cant be shown
        $output .= $this->print_error_warning();

      }

    }

  }

}
