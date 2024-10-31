<?php

// Block direct access to file
defined( 'ABSPATH' ) or die( 'Not Authorized!' );

class PRESS_TUBE_YTClient {

  /**
   * The YouTube Data api key
   * @var string
   */
	protected $apiKey;

  /**
   * The YouTube channel id
   * @var string
   */
	private $channelId;

  /**
   * The defaults and required settings for the api to work
   * @var array
   */
	private $defaults = array(
		'key' => null,
		'channelId' => null,
		'part' => 'snippet'
	);

	// all the youtube data api paths
	private $youtubeV3 = "https://www.googleapis.com/youtube/v3";
	private $youtubeV3search = "https://www.googleapis.com/youtube/v3/search";
	private $youtubeV3playlistitems = "https://www.googleapis.com/youtube/v3/playlistItems";
	private $youtubeV3playlists = "https://www.googleapis.com/youtube/v3/playlists";
	private $youtubeV3subscriptions = "https://www.googleapis.com/youtube/v3/subscriptions";
	private $youtubeV3videos = "https://www.googleapis.com/youtube/v3/videos";
	private $youtubeV3channels = "https://www.googleapis.com/youtube/v3/channels";
	private $youtubeV3comments = "https://www.googleapis.com/youtube/v3/comments";

	/**
	* Start the client by setting the key
	* and other optional options
	*/
	public function __construct($options = array()) {

		foreach ($this->defaults as $key => $value) {
      if( isset($options[$key]) && !empty($options[$key]) ) {
        $this->defaults[$key] = $options[$key];
      }
		}

	}

	/**
	 * Build the request url
	 * remove any empty param attribute
	 * and build the query url
	 */
	private function prepare_url($url, $params) {
		// remove empty elements
		$params = array_filter($params, function($k) {
			return !empty($k);
		});
		// build url
		$url = $url . '?' . http_build_query($params);
		// return url string
		return $url;
	}

  // Force request return
  private function get_content($url) {

    $context = stream_context_create(array(
      'http' => array(
        'ignore_errors' => true
      )
    ));

    return @file_get_contents( $url, false, $context );

  }

	/**
	* Search in youtube passing options
	* @param  array  $options
	* @return [json] The response from youtube
	*/
	public function search($options = array()) {
		$data = array_merge($this->defaults, $options);
		$url = $this->prepare_url( $this->youtubeV3search, $data );
		$response = $this->get_content( $url );
		return json_decode($response);
	}


	/**
	* Get youtube channels passing options
	* @param  array  $options
	* @return [json] The response from youtube
	*/
	public function get_channels($options = array()) {
		$data = array_merge($this->defaults, $options);
		$url = $this->prepare_url( $this->youtubeV3channels, $data );
		$response = $this->get_content( $url );
		return json_decode($response);
	}

	/**
	* Get youtube channels playlist items by id
	* @param  array  $options
	* @return [json] The response from youtube
	*/
	public function get_playlist_items($options = array()) {
		$data = array_merge($this->defaults, $options);
		$url = $this->prepare_url( $this->youtubeV3playlistitems, $data );
		$response = $this->get_content( $url );
		return json_decode($response);
	}

	/**
	* Get youtube channels playlists
	* @param  array  $options
	* @return [json] The response from youtube
	*/
	public function get_playlists($options = array()) {
		$data = array_merge($this->defaults, $options);
		$url = $this->prepare_url( $this->youtubeV3playlists, $data );
		$response = $this->get_content( $url );
		return json_decode($response);
	}

	/**
	* Get youtube subscriptions passing options
	* @param  array  $options
	* @return [json] The response from youtube
	*/
	public function get_subscriptions($options = array()) {

		$data = array_merge($this->defaults, $options);
		// build the url
		$youtubeV3subscriptions = $this->youtubeV3subscriptions . '?' . http_build_query($data);
		$response = $this->get_content( $youtubeV3subscriptions );
		return json_decode($response);
	}

	/**
	* Get youtube videos passing options
	* @param  array  $options
	* @return [json] The response from youtube
	*/
	public function get_videos($options = array()) {
		$data = array_merge($this->defaults, $options);
		$url = $this->prepare_url( $this->youtubeV3videos, $data );
		$response = $this->get_content( $url );
		return json_decode($response);
	}

	/**
	* Get youtube comments passing options
	* @param  array  $options
	* @return [json] The response from youtube
	*/
	public function get_comments($options = array()) {
		$data = array_merge($this->defaults, $options);
		// build the url
		$youtubeV3comments = $this->youtubeV3comments . '?' . http_build_query($data);
		$response = $this->get_content( $youtubeV3comments );
		return json_decode($response);
	}

}
