<?php

// Block direct access to file
defined( 'ABSPATH' ) or die( 'Not Authorized!' );

class PRESS_TUBE_Option_Channel_Playlists extends PRESS_TUBE_Option {

  /**
	 * @var  string  option name
	 */
	public $option = 'channel_playlists';

	public $option_name = 'channel_playlists';

  protected function __construct() {
		parent::__construct();

	}

  /**
	 * Get the singleton instance of this class
	 *
	 * @return object
	 */
	public static function get_instance() {
		if ( ! ( self::$instance instanceof self ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

  public function sanitize_callback($input) {
    return $input;
  }

}
