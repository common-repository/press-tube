<?php

// Block direct access to file
defined( 'ABSPATH' ) or die( 'Not Authorized!' ); ?>

<div class="wrap wp-tube-wrap">

  <!-- the plugin welcome box -->
  <div class="card welcome-box">

    <div id="header-image"></div>

    <p><?php _e( 'Press Tube is a <b>complete</b>, all in one, YouTube solution for WordPress installations. Is the easy way to access to all your channel content <b>directly from WordPress</b>.', 'press-tube' ); ?></p>
    <p><?php _e( 'Find easily content and share it on your posts. Manage <b>Custom Playlists</b> inside WordPress. Customize your site view with a set of <b>Widgets</b> and <b>Shortcodes</b> for every eventuality.', 'press-tube' ); ?></p>
    <p><?php _e( 'Connect directly with your YouTube channel to use your content inside WordPress, you can have access to playlists and liked videos.', 'press-tube' ); ?></p>

    <?php if( !PRESS_TUBE_Settings::get_option('main', 'key') ): ?>

      <div class="frame-wrapper">
        <iframe src="https://www.youtube.com/embed/JbWnRhHfTDA" width="500" height="350"></iframe>
      </div>

      <p><?php _e( 'Once you have done remember to save the key in the settings box below, to enable the plugin.', 'press-tube' ); ?></p>

      <div class="questions-box">
        <p class="question"><strong><?php _e( 'Where I can get a key?', 'press-tube' ); ?></strong></p>
        <p class="answer"><?php _e( 'You can get a valid Api Key to use in your site visiting <a href="https://console.developers.google.com/apis/credentials">Google Api Console</a>, follow the steps in the video to create a new project, we suggest to call it as your website name, than enable the YouTube Data Api and save the key in the plugin settings to enable all the functionality.', 'press-tube' ); ?></p>
      </div>

      <div class="questions-box">
        <p class="question"><strong><?php _e( 'Why I have to generate a key?', 'press-tube' ); ?></strong></p>
        <p class="answer"><?php _e( "Using our key for all installations of the plugin will risk incurring usage limits, to avoid this and to avoid having to make the plugin free of charge we thought to use for each one to its own key.", 'press-tube' ); ?></p>
      </div>

    <?php else: ?>

      <div class="plugin-feedback">

        <h3><?php _e( 'Did you liked the plugin?', 'press-tube' ); ?></h3>
        <p><?php _e( "Share it on your favorite social media and if you're feeling generous, donate to the authors.", 'press-tube' ); ?></p>

        <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
          <input type="hidden" name="cmd" value="_s-xclick">
          <input type="hidden" name="hosted_button_id" value="D3S5N3Y93GT5Y">
          <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
          <img alt="" border="0" src="https://www.paypalobjects.com/it_IT/i/scr/pixel.gif" width="1" height="1">
        </form>

      </div>

    <?php endif; ?>

  </div>

  <?php
  // $api_key = PRESS_TUBE_Settings::get_option('main', 'key');
  // $channel_id = PRESS_TUBE_Settings::get_option('main', 'channelId');
  //
  // // Next Versions
  // // Show the dashboard with channel informations
  // if( PRESS_TUBE_Utils::validate_key($api_key) && PRESS_TUBE_Utils::validate_channel($channel_id) ):
  //   require_once( PRESS_TUBE_DIR_PATH . '/views/settings-dashboard.php' );
  // endif; ?>

  <!-- the main plugin settings -->
  <div class="card settings">

    <form method="post" action="options.php">
      <?php settings_fields( PRESS_TUBE_Settings::get_group_name('main') ); ?>
      <?php do_settings_sections( PRESS_TUBE_Settings::$page_id ); ?>
      <?php submit_button(); ?>
    </form>

  </div>

</div>
