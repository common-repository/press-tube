<?php

// Block direct access to file
defined( 'ABSPATH' ) or die( 'Not Authorized!' );

require_once( PRESS_TUBE_WIDGETS . 'class-widget.php' );

class PRESS_TUBE_Videos extends PRESS_TUBE_Widget {

	/**
	 * The widget options displayed in backend
	 * @var array
	 */
	public $options = array(
		'title' => array(
			'label' => 'Title:',
			'type' => 'text',
			'default' => 'Videos'
		),
		'channelId' => array(
			'label' => 'Channel Id:',
			'type' => 'text',
			'default' => null
		),
		'q' => array(
			'label' => 'Query String:',
			'type' => 'text',
			'default' => ''
		),
		'order' => array(
			'label' => 'Order By:',
			'type' => 'select',
			'default' => 'relevance',
			'options' => array(
				'date' => 'Date',
				'rating' => 'Rating',
				'relevance' => 'Relevance',
				'videoCount' => 'Video Count',
				'viewCount' => 'View Count',
				'title' => 'Title'
			)
		),
		'maxResults' => array(
			'label' => 'Max Results:',
			'type' => 'number',
			'default' => 5
		),
		'show_thumbnail' => array(
			'label' => 'Show Thumbnails',
			'type' => 'checkbox',
			'default' => '1'
		),
		'display_mode' => array(
			'label' => 'Display Mode',
			'type' => 'select',
			'default' => 'normal',
			'options' => array(
				'normal' => 'Normal',
				'list' => 'List',
				// TODO: Enable the slider mode in next version when style is completed
				// 'slider' => 'Slider'
			)
		)
	);

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {

		$widget_ops = array(
			'classname' => 'wt_video_widget',
			'description' => __( 'Display videos list from YouTube with many options.', 'press-tube' ),
		);

		parent::__construct( 'wt_video_widget', 'Youtube Videos', $widget_ops );


		$this->options['channelId']['default'] = PRESS_TUBE_Settings::get_option('main', 'channelId');

	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {

		// Ensure default properties
		$instance = $this->ensure_defaults($instance);

		// Check if channelId is valid
		if( !empty($instance['channelId']) && !PRESS_TUBE_Utils::validate_channel($instance['channelId']) ) {
			$instance['channelId'] = '';
		}

		// Store instance object for query arguments
    $searchArgs = array_merge($instance, array(
			'type' => 'video'
		));

		// Unset title since is not a valid argument for YouTube Api
		// and can create errors or warning if passed
    unset($searchArgs['title']);
    unset($searchArgs['show_thumbnail']);

		global $WT;

		// Get the youtube results for the current options
    $results = $WT::$YoutubeClient->search( $searchArgs );

		// No results, return something usefull to inform the user
		if( !isset($results->items) || empty($results->items) ) {
			$this->print_empty_result($args, $instance);
			return;
		}

		print( $args['before_widget'] );
		print( $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'] );

		printf('<ul class="press-tube-widget video-results video-results-container %s">', $instance['display_mode']);

		foreach ($results->items as $result) {

			if( !$instance['show_thumbnail'] ) {
				$this->print_list_result($result);
			} else {
				$this->print_thumbnail_result($result);
			}

		}

		print('</ul>');

		print( $args['after_widget'] );

	}

	/**
	 * Print the some text to inform widget is empty
	 * @method print_empty_result
	 */
	private function print_empty_result($args, $instance) {
		print( $args['before_widget'] );
		print( $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'] );
		print( '<p>' . __('No results found.', 'press-tube') . '</p>' );
		print( $args['after_widget'] );
	}

	private function print_list_result($result) {

		return printf(
			'<li class="video-result"><a href="%s" target="_blank"><div class="video-title"><p>%s</p></div></a></li>',
			PRESS_TUBE_Utils::get_watch_url($result->id->videoId), $result->snippet->title
		);

	}

	private function print_thumbnail_result($result) {

		// date('y-m-d H:i', strtotime($result->snippet->publishedAt))

		return printf(
			'<li class="video-result">
				<a href="%s" target="_blank">
					<div class="video-thumbnail"><img src="%s" title="%s" /></div>
					<div class="video-title"><p>%s</p></div>
				</a>
			</li>',
			PRESS_TUBE_Utils::get_watch_url($result->id->videoId),
			$result->snippet->thumbnails->medium->url, $result->snippet->title,
			$result->snippet->title
		);

	}


	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {

		foreach ($this->options as $option_name => $option_details) {

			$instance[$option_name] = !isset( $instance[$option_name] ) ? $option_details['default'] : $instance[$option_name];

			if( $option_details['type'] === 'select' ) {
				$this->print_select_input( $option_name, $option_details, $instance[$option_name] );
			} else if( $option_details['type'] === 'checkbox' ) {
				$this->print_checkbox_input( $option_name, $option_details, $instance[$option_name] );
			} else {
				$this->print_standard_input( $option_name, $option_details, $instance[$option_name] );
			}

		}

  }

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {
    return $new_instance;
	}

}
