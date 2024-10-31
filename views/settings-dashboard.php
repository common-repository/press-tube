<?php

// Block direct access to file
defined( 'ABSPATH' ) or die( 'Not Authorized!' );

$wp_playlists_count = wp_count_posts( 'playlist' );
$wp_playlists = get_posts(array(
  'post_type' => 'playlist'
));

$channelPlaylists = PRESS_TUBE_Settings::get_options('channel_playlists');

$channelDetails = PRESS_TUBE_Settings::get_options('channel_details');

// Last safe check..
if( !isset($channelDetails) || empty($channelDetails) ) {
  return;
} ?>

<div class="card dashboard">

  <h2><?php _e('Dashboard', 'press-tube'); ?></h2>

  <div class="dashboard-section">

    <?php if( !empty($channelDetails) ):

      // var_dump($channelDetails); ?>

      <div class="channel-details">

        <div class="channel-banner">
          <img src="<?php echo $channelDetails['banner']; ?>" alt="<?php echo $channelDetails['title']; ?>">
        </div>

        <h3 class="channel-title">
          <img src="<?php echo $channelDetails['thumbnail']; ?>" alt="<?php echo $channelDetails['title']; ?>">
          <a href="<?php echo PRESS_TUBE_Utils::get_channel_url($channelDetails['id']); ?>" target="_blank"><?php echo $channelDetails['title']; ?></a>
        </h3>

        <div class="channel-subscribers">
          <p><?php _e('Subscribers', 'press-tube');?> <span class="badge badge-default"><?php echo $channelDetails['subscriberCount']; ?></span></p>
        </div>

        <p class="channel-description"><?php echo $channelDetails['description']; ?></p>

      </div>

    <?php endif; ?>

    <div id="playlists" class="playlists-container dashboard-box half  col-md-6">

      <h4>
        <a href="https://www.youtube.com/view_all_playlists" target="_blank"><?php _e( 'Channel Playlists', 'press-tube' ); ?></a>
      </h4>

      <?php if( !empty($channelPlaylists) ): ?>

        <p class="description"><?php printf( __('There are %s playlists imported from YouTube', 'press-tube'), count($channelPlaylists) ); ?></p>

        <div class="list-options">

          <div class="search-container">
            <input type="text" class="search" placeholder="<?php _e( 'Search', 'press-tube' ); ?>">
          </div>

          <div class="sort-container">
            <button type="button" class="button sort" data-sort="title">Title</button>
            <button type="button" class="button sort" data-sort="count">Count</button>
          </div>

        </div>

        <ul class="list">

          <?php foreach ($channelPlaylists as $playlist) { ?>

            <li>
              <a href="<?php printf('https://www.youtube.com/playlist?list=%s', $playlist['id']); ?>" target="_blank" title="See on YouTube">
                <span class="title"><?php echo $playlist['title']; ?></span>
                <span class="count"><?php echo $playlist['count']; ?></span>
              </a>
            </li>

          <?php } ?>

        </ul>

        <ul class="pagination"></ul>

      <?php else: ?>

        <p><?php _e( "It looks like you don't have any playlist on your channel.", "press-tube"); ?></p>

      <?php endif; ?>

    </div>

    <?php if( post_type_exists('playlist') ): ?>

      <div id="site-playlist" class="dashboard-box half col-md-6">

        <h4><a href="<?php echo admin_url('edit.php?post_type=playlist'); ?>"><?php _e( 'Site Playlists', 'press-tube' ); ?></a></h4>

        <?php if( !empty($wp_playlists) ): ?>

          <p class="description"><?php printf( __('There are %s custom playlists on you site.', 'press-tube'), count($wp_playlists) ); ?></p>

          <ul class="list">

            <?php foreach ($wp_playlists as $playlist) {

              $playlist_count = press_tube_get_the_playlist_items($playlist->ID);
              $playlist_count = $playlist_count ? count($playlist_count) : 0; ?>

              <li>
                <a href="<?php echo admin_url( sprintf('post.php?post=%s&action=edit', $playlist->ID) ); ?>" target="_blank" title="See on YouTube">
                  <span class="title"><?php echo $playlist->post_title; ?></span>
                  <span class="count"><?php echo $playlist_count; ?></span>
                </a>
              </li>

              <?php } ?>

            </ul>

          <?php else: ?>

            <p><?php _e( "You don't have any custom playlist yet, do you want to create a new one?", "press-tube"); ?></p>

          <?php endif; ?>

        </div>

    <?php endif; ?>

  </div>

</div>
