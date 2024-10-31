<?php

// Block direct access to file
defined( 'ABSPATH' ) or die( 'Not Authorized!' );

/**
 * The class that handle the page post_type specific metaboxes
 * @method __construct
 */
class PRESS_TUBE_Page {

  private $channelPlaylists;

  private $sitePlaylists;

  public function __construct() {

    $this->channelPlaylists = PRESS_TUBE_Settings::get_options('channel_playlists');
    $this->sitePlaylists = get_posts( 'post_type=playlist' );

    if( is_admin() ) {
      add_action( 'add_meta_boxes', array($this, 'add_page_metaboxes'), 10, 2 );
    }

  }

  /**
   * Add plugin metabox to page post_type
   * @method add_page_metaboxes
   */
  function add_page_metaboxes($post_type, $post) {
    add_meta_box( 'wt-page', __('Press Tube', 'press-tube'), array($this, 'print_page_metabox'), 'page', 'normal' );
  }

  /**
   * Print the playlist selector in pages post type
   * @method print_page_metabox
   * @param object $post The current post
   */
  function print_page_metabox($post) { ?>

    <div class="meta-section">

      <?php if( empty($this->channelPlaylists) ): ?>

        <div class="playlist-empty">

          <div class="alert">
            <p><?php _e( "You don't have any playlist imported from YouTube channel :(", "press-tube" ); ?></p>
          </div>

        </div>

      <?php else: ?>

        <div class="shortcode">

          <div class="shortcode-options-container">

            <p><?php _e( 'Customize the shortcode to use in your pages and posts.', 'press-tube' ); ?></p>

            <div class="playlist-view">
              <a id="wt-playlist-link" target="_blank" href="https://www.youtube.com/playlist?list="><?php _e( 'View playlist on YouTube', 'press-tube' ); ?></a>
            </div>

            <div class="shortcode-options">

              <div class="options-main">
                <select id="wt-playlist-select" class="" name="id" value="<?php echo $current_value; ?>">
                  <option value=""><?php _e('No Playlist', 'press-tube'); ?></option>
                  <?php foreach ($this->channelPlaylists as $playlist) {
                    printf('<option value="%s">%s</option>', $playlist['id'], $playlist['title']);
                  } ?>
                </select>
              </div>

              <div class="options-secondary" style="display:none;">

                <div class="form-group">

                  <label for=""><?php _e('Display Mode', 'press-tube'); ?></label>
                  <select class="" name="display_mode">
                    <option value="default"><?php _e('Default', 'press-tube'); ?></option>
                    <option value="list"><?php _e('List', 'press-tube'); ?></option>
                    <option value="slider"><?php _e('Slider', 'press-tube'); ?></option>
                    <option value="gallery"><?php _e('Gallery', 'press-tube'); ?></option>
                  </select>

                </div>

              </div>

            </div>

          </div>

          <div class="shortcode-result-container">
            <pre class="shortcode-result"><code></code></pre>
          </div>

          <div class="shortcode-actions">
            <button type="button" class="button shortcode-add" name="Add Shortcode"><?php _e('Add Shortcode', 'press-tube'); ?></button>
          </div>

        </div>

      <?php endif; ?>

    </div><?php

  }

}
