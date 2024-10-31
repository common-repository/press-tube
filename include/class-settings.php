<?php

// Block direct access to file
defined( 'ABSPATH' ) or die( 'Not Authorized!' );

require_once( PRESS_TUBE_SETTINGS . 'class-option.php' );
require_once( PRESS_TUBE_SETTINGS . 'class-option-main.php' );
require_once( PRESS_TUBE_SETTINGS . 'class-option-channel-details.php' );
require_once( PRESS_TUBE_SETTINGS . 'class-option-channel-playlists.php' );

class PRESS_TUBE_Settings {

  /**
   * The settings page id
   */
  public static $page_id = 'press-tube';

  private static $instance;

  /**
   * All the plugin options and classnames
   * @var array
   */
  private static $options = array(
    'main' => 'PRESS_TUBE_Option_Main',
    'channel_details' => 'PRESS_TUBE_Option_Channel_Details',
    'channel_playlists' => 'PRESS_TUBE_Option_Channel_Playlists'
  );

  /**
   * The option classes instances
   * @var array
   */
  private static $optionsInstances = array();

  protected function __construct() {

    // Build all options
    foreach (self::$options as $option_name => $option_class) {
      $instance = call_user_func( array( $option_class, 'get_instance' ) );
      self::$optionsInstances[$option_name] = $instance;
    }

    add_action( 'admin_menu', array($this, 'add_options_page') );

	}

  /**
   * Add plugin option page for class id
   * @method add_options_page
   */
  public function add_options_page() {

    add_options_page(
      __('Press Tube', 'press-tube'),
      __('Press Tube', 'press-tube'),
      'administrator',
      self::$page_id,
      array($this, 'do_settings_page')
    );

  }

  /**
  * Plugin main settings page
  * @method do_settings_page
  */
  public function do_settings_page() {

    ob_start();
    require_once(PRESS_TUBE_DIR_PATH . '/views/settings-page.php');
    print( ob_get_clean() );

  }

  public static function get_instance() {
		if ( ! ( self::$instance instanceof self ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

  /**
   * Get an option by name or a option group entirely
   * @method get_option
   */
  public static function get_option($option_group, $option_name) {

    if( isset(self::$optionsInstances[$option_group]) ) {
      return self::$optionsInstances[$option_group]->get_option($option_name);
    }

  }

  public static function update_options($option_group, $option_value) {

    if( isset(self::$optionsInstances[$option_group]) ) {
      return self::$optionsInstances[$option_group]->update_options($option_value);
    }

  }

  public static function clear_option($option_group) {
    if( isset(self::$optionsInstances[$option_group]) ) {
      return self::$optionsInstances[$option_group]->clear_options();
    }
  }

  /**
   * Get all the options instances
   * @method get_options
   */
  public static function get_options($option_group) {

    if( isset(self::$optionsInstances[$option_group]) ) {
      return self::$optionsInstances[$option_group]->get_options();
    }

  }

  /**
   * Get the registered options names
   * @method get_options_names
   */
  public static function get_options_names() {
    return array_keys( self::$options );
  }

  /**
   * Get the group name for a particular option set
   * @method get_group_name
   */
  public static function get_group_name($option_group) {
    return self::$optionsInstances[$option_group]->group_name;
  }


}
