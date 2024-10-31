<?php

// Block direct access to file
defined( 'ABSPATH' ) or die( 'Not Authorized!' );

/**
 * The shortcode to display a subscribe button for a particular channel
 * if no channel is specified the settings one will be used if available
 */
class PRESS_TUBE_Shortcode_Live_Chat {

  /**
   * The shortcode activation name
   * @var string
   */
  private $shortcode_name = 'live-chat';

  /**
   * The live chat embed url
   * @var string
   */
  private $embed_url = "https://www.youtube.com/live_chat?v=%s&embed_domain=%s";

  /**
   * The current site domain (server name)
   * @var string
   */
  private $embedDomain;

  /**
   * The class instance
   * @var object
   */
  private static $instance;

  public function __construct() {

    $this->embedDomain = $_SERVER['SERVER_NAME'];

    add_shortcode( $this->shortcode_name, array($this, 'do_shortcode') );

  }

  public static function get_instance() {
    if ( ! ( self::$instance instanceof self ) ) {
			self::$instance = new self();
		}
		return self::$instance;
  }

  /**
   * The shortcode print function
   * @method do_shortcode
   */
  public function do_shortcode($atts, $content = null) {

    // Get the shortcode options
    $options = shortcode_atts( array(
  		'id' => null,
      'width' => null,
      'height' => null
  	), $atts );

    // TODO: If admin or editor put link to edit page to fix it
    if( !PRESS_TUBE_Utils::validate_video_id($options['id']) ) {
      return;
    }

    // Get the main plugin object
    global $WT;

    // Try to get the video informations to check
    // if the streaming is active
    $videoDetails = $WT::$YoutubeClient->get_videos(array(
      'part' => 'snippet',
      'id' => $options['id']
    ));

    // If video doesn't exist
    if( isset($videoDetails->errors) || empty($videoDetails->items) ) {
      return;
    }

    // Print some fallback stuff to be frontend friendly
    if( $videoDetails->items[0]->snippet->liveBroadcastContent === 'none' ) {
      print 'The video is not streaming.';
      return;
    }

    $output = '<div class="press-tube-shortcode live-chat livestream-chat">';

    // Build the chat embed url with id and current domain
    $livechaturl = sprintf( $this->embed_url, $options['id'], $this->embedDomain );

    $output .= sprintf(
      '<iframe width="560" height="315" src="%s" frameborder="0" allowfullscreen></iframe>', $livechaturl
    );

    $output .= '</div>';

    return $output;

  }

}
