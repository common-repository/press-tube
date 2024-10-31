<?php

/**
 * Press Tube
 *
 * @package     Press Tube
 * @author      codekraft-studio
 * @copyright   2017 Press Tube
 * @license     GPL2
 *
 * Plugin Name: Press Tube
 * Description: The easiest way to integrate YouTube in your WordPress site, with many functionality.
 * Version:     0.0.3
 * Author:      codekraft-studio
 * Text Domain: press-tube
 * License:     GPL2
 *
 */

// Block direct access to file
defined( 'ABSPATH' ) or die( 'Not Authorized!' );

// Plugin Defines
define( "PRESS_TUBE_FILE", __FILE__ );
define( "PRESS_TUBE_DIR", dirname(__FILE__) );
define( "PRESS_TUBE_INCLUDE", dirname(__FILE__) . '/include/' );
define( "PRESS_TUBE_SHORTCODES", PRESS_TUBE_INCLUDE . 'shortcodes/' );
define( "PRESS_TUBE_SETTINGS", PRESS_TUBE_INCLUDE . 'settings/' );
define( "PRESS_TUBE_WIDGETS", PRESS_TUBE_INCLUDE . 'widgets/' );
define( "PRESS_TUBE_BASENAME", plugin_basename( __FILE__ ) );
define( "PRESS_TUBE_DIR_PATH", plugin_dir_path( __FILE__ ) );
define( "PRESS_TUBE_URL", plugins_url( null, __FILE__ ) );

// Require the main class file
require_once( PRESS_TUBE_INCLUDE . 'class-main.php' );

// Init all the plugin
$WT = new PRESS_TUBE;
