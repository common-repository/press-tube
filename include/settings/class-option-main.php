<?php

// Block direct access to file
defined( 'ABSPATH' ) or die( 'Not Authorized!' );

class PRESS_TUBE_Option_Main extends PRESS_TUBE_Option {

  /**
	 * @var  string  option name
	 */
	public $option = 'main';
	public $option_name = 'main';

  /**
   * The settings section title
   * @var string
   */
  public $section_title = 'Main Settings';

  // Settings fields for this section
  public $settings_fields = array(
    'key' => array(
      'title' => 'Api Key',
      'description' => "Enter a valid api key obtained from Google Developer Console. It's required for enable plugin functionalities.",
      'type' => 'text',
      'required' => false
    ),
    'channelId' =>  array(
      'title' => 'Channel Id',
      'description' => "Enter your channel id, if you don't know how to get it, visit <a href='https://www.youtube.com/account_advanced' target='_blank'>Youtube Account Advanced</a> page.",
      'type' => 'text',
      'required' => false
    )
  );

  protected function __construct() {
		parent::__construct();
    add_action( 'admin_notices', array($this, 'notify_missing_api_key') );
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

    // validate the api key
    if( isset($input['key']) ) {

      if( !PRESS_TUBE_Utils::validate_key($input['key']) ) {

        add_settings_error( 'wt-key-error', null, __( 'The Api Key is empty or not valid, should contain 39 word characters.', 'press-tube' ), 'error' );
        $input['key'] = null;

      } else {

        // Do a simple random request to see if is working
        $request = PRESS_TUBE::$YoutubeClient->search(array(
          'key' => $input['key'],
          'channelId' => '',
          'maxResults' => '1'
        ));

        // If error is set inform the user and reset the variable to null
        if( isset($request->error) ) {

          switch ($request->error->code) {
            case 400:
              // Show the validation error
              add_settings_error( 'wt-key-error', null, __( 'Something went wrong with the key you entered, check you have the right key obtained on Google Api Console.', 'press-tube' ), 'error' );
              break;

            default:
              // Show the validation error for debug purposes
              add_settings_error( 'wt-key-error', null, $request->error->errors[0]->message, 'error' );
              break;
          }

          // Set to null to prevent plugin activation
          // and to skip the next check
          $input['key'] = null;
        }

      }

      // Check if the channel is a existing one
      if( isset($input['channelId']) && !empty($input['channelId']) ) {

        // If the channel is bad format alert the user
        if( !PRESS_TUBE_Utils::validate_channel($input['channelId']) ) {

          add_settings_error( 'wt-channel-error', null, __( 'The Channel Id you entered is invalid, it should be 24 word characters long and pointing to a real YouTube channel.', 'press-tube' ), 'error' );

          // Clear bad channel id to avoid strnage beheviours for now
          $input['channelId'] = false;

        // If the channel id is valid check the key than try to
        // get the channel status to see if channel is exists
        } else if( PRESS_TUBE_Utils::validate_key($input['key']) ) {

          // Check if channel exists
          $request = PRESS_TUBE::$YoutubeClient->get_channels(array(
            'id' => $input['channelId'],
            'part' => 'status'
          ));

          // if no channel were found alert the user
          if( empty($request->items) ) {

            add_settings_error( 'wt-channel-error', null, __( 'The Channel Id is not valid, is not matching with any existing YouTube channel.', 'press-tube' ), 'error' );

          } else {

            // Get again the channel playlist and details
            PRESS_TUBE::get_channel_playlists();

          }

        }

      }

    }

    return $input;

  }

  /**
   * Notify the user the plugin will not work until
   * he enter a valid Youtube Api key
   * @method notify_missing_api_key
   */
  public function notify_missing_api_key() {

    // Dont show the notification is plugin settings page
    // or is the key is already set
    if( PRESS_TUBE_Settings::get_option('main', 'key') || get_current_screen()->parent_base !== 'plugins'  ) {
      return;
    }

    // Get the plugin settings url
    $settings_url = admin_url( 'options-general.php?page=' . PRESS_TUBE_Settings::$page_id );

    // The info message to display with link to settings page
  	$message = sprintf(
      __( 'Press Tube: It looks like you dont have a Api Key, visit the %s page otherwise the plugin functions will not be available.', 'press-tube' ),
      '<a href="' . $settings_url . '" target="_self">settings</a>'
    );

    // Show the notify message
  	printf( '<div class="notice notice-info is-dismissible"><p>%s</p></div>', $message );

  }



}
