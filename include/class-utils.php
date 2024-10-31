<?php

// Block direct access to file
defined( 'ABSPATH' ) or die( 'Not Authorized!' );

/**
 * Various methods to do some YouTube and plugin related stuff
 */
class PRESS_TUBE_Utils {

  // The youtube urls
  private static $youtube_watch_url = 'https://www.youtube.com/watch?v=%s';
  private static $youtube_embed_url = 'https://www.youtube.com/embed/%s';
  private static $youtube_channel_url = 'https://www.youtube.com/channel/%s';

  /**
   * Check if the channelId is not empty
   * and match the desired length and characters pattern
   * @method validate_channel
   */
  public static function validate_channel($channelId) {
    return preg_match( '/^[\w-]{24}$/', $channelId );
  }

  /**
   * Check if the api key is not empty
   * and match the desired length and characters pattern
   * @method validate_key
   */
  public static function validate_key($apiKey) {
    return preg_match( '/^[\w-]{39}$/', $apiKey );
  }

  /**
   * Check if the api key is not empty
   * and match the desired length and characters pattern
   * @method validate_video_id
   */
  public static function validate_video_id($videoId) {
    return preg_match( '/^[\w-]{11}$/', $videoId );
  }

  /**
   * Check if the playlist id is not empty
   * and match the desired length and characters pattern
   * @method validate_playlist
   */
  public static function validate_playlist($playlistId) {
    return preg_match( "/([a-zA-Z0-9_\-]{12})/", $playlistId );
  }

  /**
   * Get the youtube watch ulr from a given video id
   * @method get_watch_url
   */
  public static function get_watch_url($videoId) {
    return sprintf(self::$youtube_watch_url, $videoId);
  }

  /**
   * Get the youtube embed ulr from a given video id
   * @method get_embed_url
   */
  public static function get_embed_url($videoId) {
    return sprintf(self::$youtube_embed_url, $videoId);
  }

  /**
   * Build the channel url for a given channelId
   * @method get_channel_url
   */
  public static function get_channel_url($channelId) {
    return sprintf(self::$youtube_channel_url, $channelId);
  }

}
