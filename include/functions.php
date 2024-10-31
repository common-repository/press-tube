<?php

// Block direct access to file
defined( 'ABSPATH' ) or die( 'Not Authorized!' );

/**
* Get the playlist items video for a given ID
* @method press_tube_get_the_playlist_items
* @param int $id The post id
* @return array The playlist items array
*/
if ( !function_exists( 'press_tube_get_the_playlist_items' ) ) {

  function press_tube_get_the_playlist_items($id = null) {
    $id = $id ? $id : get_the_ID();
    return get_post_meta( $id, PRESS_TUBE_Playlist::$meta_key, true );
  }

}

if ( !function_exists( 'press_tube_get_the_page_video_embed' ) ) {

  function press_tube_get_the_page_video_embed($id = null, $options = array()) {

    if( $video = get_page_main_video($id) ) {

      $url = $video->urls->embed;

      if( is_array($options) ) {
        $url .= '?' . http_build_query($options);
      }

      return sprintf( '<iframe width="560" height="315" src="%s" frameborder="0" allowfullscreen></iframe>', $url );

    }

  }

}

if ( !function_exists( 'press_tube_the_page_video_embed' ) ) {

  function press_tube_the_page_video_embed($id = null, $options = array()) {
    print( get_the_page_video_embed($id, $options) );
  }

}

if ( !function_exists( 'press_tube_get_page_main_video' ) ) {

  // TODO: create a 'sticky' video for the main video
  function press_tube_get_page_main_video($id = null) {

    if( $playlist_items = get_page_playlistitems($id) ) {
      return $playlist_items[0];
    }

  }

}

if ( !function_exists( 'press_tube_get_page_playlistitems' ) ) {

  function press_tube_get_page_playlistitems($id = null) {

    $id = $id ? $id : get_the_ID();

    // Check if the page has a playlist id associated
    if( !$id || !get_post_meta( $id, 'wt_playlist_id', true ) ) {
      return;
    }

    return press_tube_get_the_playlist_items( $id );

  }

}

if ( !function_exists( 'press_tube_the_page_playlist' ) ) {

  function press_tube_the_page_playlist($id = null) {

    if( !$playlist_items = get_page_playlistitems($id) ) {
      return;
    }

    foreach ($playlist_items as $playlist_item) { ?>

      <div class="video-thumb" itemscope itemtype="http://schema.org/VideoObject">

        <a href="<?php echo esc_url($playlist_item->urls->watch); ?>" target="_blank">
          <img itemprop="url" src="<?php echo $playlist_item->thumbnails->standard->url; ?>" alt="<?php echo $playlist_item->title; ?>" />
        </a>

        <div class="video-details">

          <h3 itemprop="name" class="title"><?php echo $playlist_item->title; ?></h3>

          <p itemprop="description" class="description"><?php echo wp_trim_words( $playlist_item->description ); ?></p>

          <span class="publish-date">
            <?php _e('Published at:', 'press-tube'); ?>
            <time itemprop="uploadDate" datetime="<?php echo $playlist_item->publishedAt; ?>">
              <?php echo date(DATE_RSS, strtotime($playlist_item->publishedAt)); ?>
            </time>
          </span>

        </div>

      </div><?php

    }

  }

}
