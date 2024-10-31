<?php

// Block direct access to file
defined( 'ABSPATH' ) or die( 'Not Authorized!' );

/**
 * The shortcode to display a subscribe button for a particular channel
 * if no channel is specified the settings one will be used if available
 */
class PRESS_TUBE_Shortcode_Subscribe {

  /**
   * The shortcode activation name
   * @var string
   */
  private $shortcode_name = 'subscribe';

  /**
   * Optional channelId get from the main settings
   * @var string
   */
  private $channelId;

  /**
   * The class instance
   * @var object
   */
  private static $instance;

  public function __construct() {

    // Store the channelId from the plugin settings
    $this->channelId = PRESS_TUBE_Settings::get_option( 'main', 'channelId' );
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
      'title' => '',
  		'channelId' => $this->channelId,
      'layout' => 'default',
      'theme' => 'default',
      'count' => 'default'
  	), $atts );

    // Exit if channelId is not valid (since is required)
    if( !PRESS_TUBE_Utils::validate_channel($options['channelId']) ) {
      return;
    }

    // Init the subscribe button container
    $output = '<div class="press-tube-shortcode subscribe subscribe-container">';

    if( !empty($options['title']) ) {
      $output .= '<div class="subscribe-title">' . esc_html($options['title']) . '</div>';
    }

    // Add the actual button
    $output .= '<div class="subscribe-content">';

    // Add the option description
    if( $content ) {
      $output .= '<p class="subscribe-description">' . esc_html($content) . '</p>';
    }

    $output .= sprintf(
      '<div class="g-ytsubscribe" data-channelid="%s" data-layout="%s" data-count="%s"></div>',
      $options['channelId'],
      $options['layout'],
      $options['theme'],
      $options['count']
    );

    $output .= '</div>';
    $output .= '</div>';

    return $output;

  }

}
