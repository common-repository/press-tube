<?php

// Block direct access to file
defined( 'ABSPATH' ) or die( 'Not Authorized!' );

require_once( PRESS_TUBE_INCLUDE . 'functions.php' );
require_once( PRESS_TUBE_INCLUDE . 'class-yt-client.php' );
require_once( PRESS_TUBE_INCLUDE . 'class-settings.php' );
require_once( PRESS_TUBE_INCLUDE . 'class-utils.php' );

class PRESS_TUBE {

  /**
   * The plugin authors donate url
   * @var string
   */
  const DONATE_URL = 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=D3S5N3Y93GT5Y';

  /**
   * The YouTube api key
   * @var string
   */
  private $api_key;

  /**
   * The channel playlists
   * @var array
   */
  private $playlists;

  /**
   * The YouTube client init with plugin settings
   * @var object
   */
  public static $YoutubeClient;

  /**
   * The plugin shortcodes array
   * @var array
   */
  private $shortcodes = array(
    'PRESS_TUBE_Shortcode_Subscribe' => PRESS_TUBE_SHORTCODES . 'subscribe.php',
    'PRESS_TUBE_Shortcode_Playlist' => PRESS_TUBE_SHORTCODES . 'playlist.php',
    'PRESS_TUBE_Shortcode_Live_Chat' => PRESS_TUBE_SHORTCODES . 'livechat.php',
  );

  /**
   * The plugin widgets array
   * @var array
   */
  private $widgets = array(
    'PRESS_TUBE_Videos' => PRESS_TUBE_WIDGETS . 'last-videos.php',
    'PRESS_TUBE_Subscribe' => PRESS_TUBE_WIDGETS . 'subscribe.php',
    'PRESS_TUBE_Live_Stream' => PRESS_TUBE_WIDGETS . 'live-stream.php'
  );

  public function __construct() {

    // Plugin uninstall hook
    register_uninstall_hook( PRESS_TUBE_FILE, array(__CLASS__, 'plugin_uninstall') );

    // Plugin activation/deactivation hooks
    register_activation_hook( PRESS_TUBE_FILE, array($this, 'plugin_activate') );
    register_deactivation_hook( PRESS_TUBE_FILE, array($this, 'plugin_deactivate') );

    // Load plugin text domain
    add_action( 'plugins_loaded', array($this, 'load_plugin_text_domain') );

    // Enqueue scripts
    add_action( 'wp_enqueue_scripts', array($this, 'plugin_enqueue_user_scripts') );
    add_action( 'admin_enqueue_scripts', array($this, 'plugin_enqueue_admin_scripts') );

    // Add some extra action links in plugin page
    add_filter( 'plugin_row_meta', array( $this, 'add_plugin_meta_links' ), 10, 2 );
    // Add extra action fields to plugin page
    add_filter( 'plugin_action_links_' . PRESS_TUBE_BASENAME, array($this, 'add_plugin_action_links') );

    // Init the plugin settings instance
    // that get options and create option pages
    PRESS_TUBE_Settings::get_instance();

    // Get the api key
    $this->api_key = PRESS_TUBE_Settings::get_option('main', 'key');

    // Init the YouTube client with the settings
    self::$YoutubeClient = new Press_Tube_YTClient( PRESS_TUBE_Settings::get_options('main') );

    // Return if key is not set since is required
    // this will stop the other part of the plugin to be loaded
    if( !PRESS_TUBE_Utils::validate_key( $this->api_key ) ) {
      return;
    }

    // Get the channel playlists if doesnt exist
    if( PRESS_TUBE_Utils::validate_channel( PRESS_TUBE_Settings::get_option('main', 'channelId') ) ) {

      // // Next Versions
      // // Init the user channel informations
      // $this->init_channel_informations();

    } else {

      PRESS_TUBE_Settings::clear_option('channel_details');
      PRESS_TUBE_Settings::clear_option('channel_playlists');

    }

    // Next Versions
    // Init or create the playlist type
    $this->init_playlist_type();

    // Load shortcodes classes and init plugin shortcodes
    $this->init_shortcodes();

    // Next Versions
    // Load all the widgets
    add_action( 'widgets_init', array($this, 'register_plugin_widgets') );

    // Add youtube frame select to all pages with media editor
    add_action( 'media_buttons', array($this, 'add_plugin_media_button'), 10 );

    add_filter( 'mce_css', array($this, 'add_plugin_editor_style') );

  }

  /**
   * Init the playlist post type and functions
   * @method init_playlist_type
   */
  function init_playlist_type() {

    // Next Version
    require_once( PRESS_TUBE_INCLUDE . 'class-playlist.php' );
    new PRESS_TUBE_Playlist();

    // require_once( PRESS_TUBE_INCLUDE . 'class-page.php' );

    // // Next Versions
    // // init playlist post type and metaboxes
    // new PRESS_TUBE_Page();

  }

  /**
   * Add and init all the plugin shortcodes
   * @method init_shortcodes
   */
  function init_shortcodes() {

    foreach ($this->shortcodes as $className => $path) {
      require_once( $path );
      call_user_func( array( $className, 'get_instance' ) );
    }

  }

  /**
   * Load and register all the plugin widgets
   * @method register_plugin_widgets
   */
  function register_plugin_widgets() {

    foreach ($this->widgets as $className => $path) {
      require_once( $path );
      register_widget( $className );
    }

  }

  /**
   * Get the playlist from the authenticated user channel
   * and store into plugin options for later use
   * only if the channelId parameter is specified otherwise will throw error
   * @method get_channel_playlists
   */
  public static function get_channel_playlists() {

    $result = [];

    // Get all the channel playlists
    $playlists = self::$YoutubeClient->get_playlists(array(
      'maxResults' => 50, // The maximum allowed
      'part' => 'snippet,contentDetails',
      'channelId' => PRESS_TUBE_Settings::get_option('main', 'channelId')
    ));

    // On error store it for later use
    if( isset($playlists->error) ) {

      // Update plugin settings
      PRESS_TUBE_Settings::update_options( 'channel_playlists', false );
      return $playlists;

    }

    // If results were found extract basic properties
    // and add it to results array
    if( $playlists && !empty($playlists->items) ) {

      foreach ($playlists->items as $playlist) {

        $result[] = array(
          'id' => $playlist->id,
          'title' => $playlist->snippet->title,
          'description' => $playlist->snippet->description,
          'thumbnails' => $playlist->snippet->thumbnails,
          'count' => $playlist->contentDetails->itemCount
        );
      }

    }

    // Update plugin settings
    PRESS_TUBE_Settings::update_options( 'channel_playlists', $result );

    return $result;

  }

  /**
   * Add extra action links to plugin page
   * @method add_plugin_action_links
   * @param array $links The current links array
   */
  function add_plugin_action_links( $links ) {

    $extraLinks = array(
      'settings' => sprintf(
        '<a href="%s">%s</a>',
        esc_url( admin_url("options-general.php?page=" . PRESS_TUBE_Settings::$page_id) ),
        __( 'Settings', 'press-tube' )
      ),
    );

    return array_merge($extraLinks, $links);

  }

  /**
   * Add some extra action links in the plugin meta
   * @method add_plugin_meta_links
   * @param array $plugin_meta The plugin meta array
   * @param string $plugin_file The plugin basename
   */
  function add_plugin_meta_links($plugin_meta, $plugin_file) {

		if( $plugin_file === PRESS_TUBE_BASENAME ) {

      $links = array(
        'donate' => sprintf( '<a href="%s" target="_blank">%s</a>', self::DONATE_URL, __( 'Donate', 'press-tube' ) )
      );

		  $plugin_meta = array_merge( $plugin_meta, $links );

		}

		return $plugin_meta;

	}

  /**
   * Init various authenticated user informations
   * and stuff that can be usefull sometimes
   * this method is called only if the api key is set
   * @method init_channel_informations
   */
  function init_channel_informations() {

    // Only if the option doesn't exist yet
    if( !PRESS_TUBE_Settings::get_options( 'channel_playlists' ) ) {
      $this->playlists = self::get_channel_playlists();
    }

    // Only if the option doesn't exist yet
    if( !PRESS_TUBE_Settings::get_options( 'channel_details' ) ) {

      $result = self::$YoutubeClient->get_channels(array(
        'id' => PRESS_TUBE_Settings::get_option('main', 'channelId'),
        'part' => 'snippet,contentDetails,statistics,brandingSettings'
      ));

      if( !isset($result->items) || empty($result->items) ) {
        return;
      }

      $channelDetails = array();
      $channelDetails['id'] = $result->items[0]->id;
      $channelDetails['title'] = $result->items[0]->snippet->title;
      $channelDetails['publishedAt'] = $result->items[0]->snippet->publishedAt;
      $channelDetails['description'] = $result->items[0]->snippet->description;
      $channelDetails['relatedPlaylists'] = (array) $result->items[0]->contentDetails->relatedPlaylists;
      $channelDetails['subscriberCount'] = $result->items[0]->statistics->subscriberCount;
      $channelDetails['banner'] = $result->items[0]->brandingSettings->image->bannerImageUrl;
      $channelDetails['thumbnail'] = $result->items[0]->snippet->thumbnails->default->url;

      // Update plugin settings
      PRESS_TUBE_Settings::update_options( 'channel_details', $channelDetails );

    }

  }

  /**
   * Print the video frame toggler media button
   * @method add_plugin_media_button
   */
  function add_plugin_media_button($editor_id) {
    print( '<button id="wt-open-frame" class="button"><span class="wp-media-buttons-icon dashicons dashicons-video-alt3"></span>YouTube</button>' );
  }

  /**
   * Plugin uninstall function
   * called when the plugin is uninstalled
   * @method plugin_uninstall
   */
  public static function plugin_uninstall() { }

  /**
  * Plugin activation function
  * called when the plugin is activated
  * @method plugin_activate
  */
  public function plugin_activate() { }

  /**
  * Plugin deactivate function
  * is called during plugin deactivation
  * @method plugin_deactivate
  */
  public function plugin_deactivate() { }

  /**
  * Plugin init function
  * init the polugin textDomain
  * @method load_plugin_text_domain
  */
  function load_plugin_text_domain() {
    load_plugin_textDomain( 'press-tube', false, dirname(PRESS_TUBE_BASENAME) . '/languages' );
  }

  /**
  * Enqueue the main Plugin admin scripts and styles
  * @method plugin_enqueue_admin_scripts
  */
  function plugin_enqueue_admin_scripts() {

    wp_register_style( 'press-tube_admin_style', PRESS_TUBE_URL . '/assets/dist/css/admin.css', array('media-views'), null );
    wp_enqueue_style('press-tube_admin_style');

    wp_register_script( 'google-api', 'https://apis.google.com/js/api.js', array(), null, true );
    wp_register_script( 'jsrender', PRESS_TUBE_URL . '/assets/dist/js/jsrender.min.js', array(), null, true );
    wp_register_script( 'listjs', PRESS_TUBE_URL . '/assets/dist/js/list.min.js', array(), null, true );

    // wp_register_script( 'clipboard.js', PRESS_TUBE_URL . '/assets/dist/js/clipboard.min.js', array(), null, true );

    wp_register_script( 'press-tube_admin_script', PRESS_TUBE_URL . '/assets/dist/js/admin.min.js', array('google-api', 'jsrender', 'listjs'), null, true );

    // Pass the YouTube API settings to javascript
    wp_localize_script( 'press-tube_admin_script', 'WPTUBE', PRESS_TUBE_Settings::get_options('main') );

    wp_enqueue_script('jquery');
    wp_enqueue_script('google-api');
    wp_enqueue_script('press-tube_admin_script');

  }

  /**
  * Enqueue the main Plugin user scripts and styles
  * @method plugin_enqueue_user_scripts
  */
  function plugin_enqueue_user_scripts() {

    wp_register_style( 'press-tube_user_style', PRESS_TUBE_URL . '/assets/dist/css/user.css', array(), null );
    wp_enqueue_style('press-tube_user_style');

    wp_register_script( 'platform.js', 'https://apis.google.com/js/platform.js', array(), null, true );
    wp_register_script( 'slick', PRESS_TUBE_URL . '/assets/dist/js/slick.min.js', array(), null, true );

    wp_register_script( 'press-tube_user_script', PRESS_TUBE_URL . '/assets/dist/js/user.min.js', array('slick', 'platform.js'), null, true );
    wp_enqueue_script('jquery');
    wp_enqueue_script('platform.js');
    wp_enqueue_script('press-tube_user_script');

  }

  /**
   * Add the plugin editor style to tinymce
   * @method add_plugin_editor_style
   */
  function add_plugin_editor_style($mce_css) {
    if ( !empty( $mce_css ) ) {
      $mce_css .= ',';
    }
    $mce_css .= PRESS_TUBE_URL . '/assets/dist/css/editor-style.css';
    return $mce_css;
  }

}
