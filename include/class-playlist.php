<?php

// Block direct access to file
defined( 'ABSPATH' ) or die( 'Not Authorized!' );

/**
 * The class that handle the Playlist custom post type creation
 * all his metaboxes and functions relative to the view, shortcodes, ...
 */
class PRESS_TUBE_Playlist {

  /**
   * The meta key name for the playlist items
   * @var string
   */
  public static $meta_key = 'wt_playlist_items';

  public function __construct() {

    // Add playlist post type
    add_action( 'init', array($this, 'playlist_post_type') );

    // If is admin side
    if( is_admin() ) {

      // Add the playlist save action
      add_action( 'save_post', array($this, 'save_playlist_metabox'), 10, 2 );

      // Remove all metaboxes and add the plugin defined ones
      add_action( 'add_meta_boxes', array($this, 'remove_undesired_metaboxes'), 99, 2 );
      add_action( 'add_meta_boxes', array($this, 'add_playlist_metaboxes'), 10, 2 );

      // Add custom columns to playlist edit page
      add_action( 'manage_playlist_posts_columns', array($this, 'add_playlist_custom_columns') );
      add_action( 'manage_playlist_posts_custom_column', array($this, 'print_playlist_custom_columns_content'), 10, 2 );

    }

  }

  /**
   * Add custom columns to playlist post type
   * @method add_playlist_custom_columns
   * @param array $columns The default columns
   */
  function add_playlist_custom_columns($columns) {

    return array_merge($columns, array(
      'count' => __('Count', 'press-tube'),
      'shortcode' => __('Shortcode', 'press-tube')
    ));

  }

  /**
   * Print the playlist items count
   * @method print_playlist_custom_columns_content
   * @param string $column The column name
   * @param int $post_id The playlist id
   */
  function print_playlist_custom_columns_content($column, $post_id) {

    if( $column == 'count' && $playlist_items = get_post_meta( $post_id, self::$meta_key, true ) ) {
      print( count($playlist_items) );
    }

    if( $column == 'shortcode' ) {
      printf( '<code>[playlist id="%s"]</code>', $post_id );
    }

  }

  /**
   * Create a private custom post type for
   * holding the press-tube playlists data
   * @method playlist_post_type
   */
  function playlist_post_type() {

    $labels = array(
      'name'                  => _x( 'Playlists', 'Post Type General Name', 'press-tube' ),
      'singular_name'         => _x( 'Playlist', 'Post Type Singular Name', 'press-tube' ),
      'menu_name'             => __( 'Playlist', 'press-tube' ),
      'name_admin_bar'        => __( 'Playlist', 'press-tube' ),
      'archives'              => __( 'Playlist Archives', 'press-tube' ),
      'attributes'            => __( 'Playlist Attributes', 'press-tube' ),
      'parent_item_colon'     => __( 'Parent Playlist:', 'press-tube' ),
      'all_items'             => __( 'All Playlists', 'press-tube' ),
      'add_new_item'          => __( 'Add New Playlist', 'press-tube' ),
      'add_new'               => __( 'Add New', 'press-tube' ),
      'new_item'              => __( 'New Playlist', 'press-tube' ),
      'edit_item'             => __( 'Edit Playlist', 'press-tube' ),
      'update_item'           => __( 'Update Playlist', 'press-tube' ),
      'view_item'             => __( 'View Playlist', 'press-tube' ),
      'view_items'            => __( 'View Playlists', 'press-tube' ),
      'search_items'          => __( 'Search Playlist', 'press-tube' ),
      'not_found'             => __( 'Not found', 'press-tube' ),
      'not_found_in_trash'    => __( 'Not found in Trash', 'press-tube' ),
      'featured_image'        => __( 'Featured Image', 'press-tube' ),
      'set_featured_image'    => __( 'Set featured image', 'press-tube' ),
      'remove_featured_image' => __( 'Remove featured image', 'press-tube' ),
      'use_featured_image'    => __( 'Use as featured image', 'press-tube' ),
      'insert_into_item'      => __( 'Insert into item', 'press-tube' ),
      'uploaded_to_this_item' => __( 'Uploaded to this item', 'press-tube' ),
      'items_list'            => __( 'Playlists list', 'press-tube' ),
      'items_list_navigation' => __( 'Playlists list navigation', 'press-tube' ),
      'filter_items_list'     => __( 'Filter items list', 'press-tube' ),
    );

    $args = array(
      'label'                 => __( 'Playlist', 'press-tube' ),
      'description'           => __( 'A YouTube playlist.', 'press-tube' ),
      'labels'                => $labels,
      'supports'              => array('title', /* Next Versions: 'thumbnail' */),
      'hierarchical'          => false,
      'public'                => false,
      'show_ui'               => true,
      'show_in_menu'          => true,
      'menu_position'         => 25, // Before the settings divider
      'menu_icon'             => 'dashicons-playlist-video',
      'show_in_admin_bar'     => true,
      'show_in_nav_menus'     => false,
      'can_export'            => true,
      'has_archive'           => false,
      'exclude_from_search'   => true,
      'publicly_queryable'    => false,
      'capability_type'       => 'page',
    );

    register_post_type( 'playlist', $args );

  }

  /**
   * Remove all the metaboxes from playlist post_type that
   * are not directly or indirectly related with the type to avoid confusion
   * @method remove_undesired_metaboxes
   */
  function remove_undesired_metaboxes($post_type, $post) {

    if( $post_type !== 'playlist' ) {
      return;
    }

    // the metaboxes to keep
    $exceptions = array(
      'submitdiv',
      'postimagediv',
      'wt-playlist',
      'wt-playlist-shortcode'
    );

    global $wp_meta_boxes;

    // loop through metaboxes and remove all but the exceptions
    foreach ($wp_meta_boxes as $page => $page_boxes) {
      if( empty($page_boxes) ) continue;
      foreach ($page_boxes as $context => $box_context) {
        if( empty($box_context) ) continue;
        foreach ($box_context as $box_type) {
          foreach ($box_type as $id => $metabox) {
            if( !in_array($id, $exceptions) ) {
              remove_meta_box($id, $page, $context);
            }
          }
        }
      }
    }

  }

  /**
   * Add custom metaboxes to playlist post type
   * @method add_playlist_metaboxes
   */
  function add_playlist_metaboxes($post_type, $post) {

    add_meta_box( 'wt-playlist', __('Playlist Items', 'press-tube'), array($this, 'print_playlist_metabox'), 'playlist' );

    // // Next versions
    // add_meta_box( 'wt-playlist-shortcode', __('Playlist Shortcode', 'press-tube'), array($this, 'print_playlist_shortcode_metabox'), 'playlist', 'normal', 'high' );

  }

  /**
   * Print the playlist item HTML element for given data
   * @method print_meta_playlist_item
   */
  function print_meta_playlist_item($item, $index) {

    $item['sortIndex'] = $index + 1;
    $item['index'] = $index;

    $output = str_replace(
			array_map(function($key){ return '{' . $key . '}'; }, array_keys($item)),
			array_values(array_map('esc_attr', $item)),
			'<div class="playlist-item">
        <div class="handle">{sortIndex}</div>
        <div class="playlist-thumb"><img src="{thumbnail}" alt="{title}"></div>
        <div class="playlist-details">
          <div class="playlist-title">
            <h3 class="video-title">
              <a href="https://www.youtube.com/watch?v={id}" target="_blank">{title}</a>
            </h3>
            <p>
              <a class="video-channel-title" href="{channelUrl}" target="_blank">{channelTitle}</a> -
              <span class="video-date">{publishedAt}</span>
            </p>
          </div>
          <div class="playlist-footer"></div>
          <div class="playlist-data hidden" style="display: none;">
            <input type="hidden" name="wt_playlist_items[{index}][id]" value="{id}" />
            <input type="hidden" name="wt_playlist_items[{index}][title]" value="{title}" />
            <input type="hidden" name="wt_playlist_items[{index}][description]" value="{description}" />
            <input type="hidden" name="wt_playlist_items[{index}][thumbnail]" value="{thumbnail}" />
            <input type="hidden" name="wt_playlist_items[{index}][publishedAt]" value="{publishedAt}" />
            <input type="hidden" name="wt_playlist_items[{index}][channelUrl]" value="{channelUrl}" />
            <input type="hidden" name="wt_playlist_items[{index}][channelTitle]" value="{channelTitle}" />
          </div>
        </div>
        <div class="playlist-item-remove"><span class="dashicons dashicons-no-alt"></span></div>
      </div>'
		);

    print $output;

  }

  /**
   * Print the playlist items (if any)
   * in a seamless metabox for playlist post type
   * @method print_playlist_metabox
   */
  function print_playlist_metabox($post) {

    // Try to get the playlist video items
    $playlist_items = get_post_meta( $post->ID, self::$meta_key, true ); ?>

    <div class="meta-section">

      <div class="meta-toolbar">

        <div class="meta-toolbar-search">
          <input type="text" name="Playlist Filter" placeholder="<?php _e( 'Type here to filter', 'press-tube' ); ?>">
        </div>

        <div class="meta-toolbar-primary">
          <button id="wt-open-frame" class="button">
            <span class="wp-media-buttons-icon dashicons dashicons-video-alt3"></span>YouTube
          </button>
        </div>

      </div>

      <div class="playlist-items-container sortable">

        <?php

        // Show the playlist results
        if( $playlist_items ):

          foreach ($playlist_items as $key => $playlist_item) {
            $this->print_meta_playlist_item($playlist_item, $key);
          }

        endif; ?>

      </div>

    </div><?php

  }

  /**
   * // TODO: make the style for this stuff, that now is disavbled
   * Print the shortcode generator box
   * @method print_playlist_shortcode_metabox
   */
  function print_playlist_shortcode_metabox($playlist) {  ?>

    <div class="meta-section">

      <div class="shortcode-options-container">

        <p><?php _e( 'Customize the shortcode to use in your pages and posts.', 'press-tube' ); ?></p>

        <div class="shortcode-options">

          <!-- the playlist id -->
          <input type="hidden" name="id" value="<?php echo $playlist->ID; ?>" readonly="true" />

          <div class="form-group">

            <label for=""><?php _e('Display Mode', 'press-tube'); ?></label>
            <select class="" name="display_mode">
              <option value="normal"><?php _e('Normal', 'press-tube'); ?></option>
              <option value="list"><?php _e('List', 'press-tube'); ?></option>
              <option value="slider"><?php _e('Slider', 'press-tube'); ?></option>
              <option value="gallery"><?php _e('Gallery', 'press-tube'); ?></option>
            </select>

          </div>

          <div class="form-group">
            <label for=""><?php _e('Width', 'press-tube'); ?></label>
            <input type="text" name="width" value="" />
          </div>

          <div class="form-group">
            <label for=""><?php _e('Height', 'press-tube'); ?></label>
            <input type="text" name="height" value="" />
          </div>

        </div>

      </div>

      <div class="shortcode-result-container">
        <pre class="shortcode-result"><code></code></pre>
      </div>

    </div><?php

  }

  /**
   * Save some playlist custom meta fields
   * @method save_playlist_metabox
   * @param int $post_id The post id
   * @param object $post The post object
   */
  function save_playlist_metabox($post_id, $post) {

    // Check if user has permissions to save data.
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
      return;
    }

    // Check if not an autosave.
    if ( wp_is_post_autosave( $post_id ) ) {
      return;
    }

    // Check if not a revision.
    if ( wp_is_post_revision( $post_id ) ) {
      return;
    }

    // Prevent other post types to be edited
    if( $post->post_type !== 'playlist' ) {
      return;
    }

    // update the playlist items
    if( isset($_POST[self::$meta_key]) && is_array($_POST[self::$meta_key]) ) {

      $reordered_items = array_values($_POST[self::$meta_key]);
      update_post_meta( $post_id, self::$meta_key, $reordered_items );

    } else {

      // Set as empty array
      update_post_meta( $post_id, self::$meta_key, array() );

    }

  }

  public static function get_playlist_items_from_youtube($playlist_id) {

    // Attempt to get playlist items for current playlist
    $query = PRESS_TUBE::$YoutubeClient->get_playlist_items(array(
      'part' => 'snippet,status',
      'playlistId' => $playlist_id,
      'maxResults' => 50 // The maximum allowed
    ));

    // if the playlist has items store into the post meta
    if( isset($query->items) && !empty($query->items) ) {

      // init empty array
      $playlist_items = [];

      foreach ($query->items as $playlist_item) {

        // skip private videos to avoid 403 errors on frontend
        // and errors while getting variables below
        if( $playlist_item->status->privacyStatus !== 'public' ) {
          continue;
        }

        $item = array();
        $item['id'] = $playlist_item->snippet->resourceId->videoId;
        $item['playlistId'] = $playlist_item->snippet->playlistId;
        $item['title'] = $playlist_item->snippet->title;
        $item['description'] = $playlist_item->snippet->description;
        $item['publishedAt'] = $playlist_item->snippet->publishedAt;
        $item['thumbnail'] = $playlist_item->snippet->thumbnails->high->url;
        $item['channelUrl'] = 'https://www.youtube.com/channel/' . $playlist_item->snippet->channelId;
        $item['channelTitle'] = $playlist_item->snippet->channelTitle;
        $item['watchUrl'] = PRESS_TUBE_Utils::get_watch_url( $item['id'] );
        $item['embedUrl'] = PRESS_TUBE_Utils::get_embed_url( $item['id'] );

        // Add to playlist items array
        $playlist_items[] = $item;

      }

      return $playlist_items;

    }

  }

}
