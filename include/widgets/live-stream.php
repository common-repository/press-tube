<?php

// Block direct access to file
defined( 'ABSPATH' ) or die( 'Not Authorized!' );

require_once( PRESS_TUBE_WIDGETS . 'class-widget.php' );

class PRESS_TUBE_Live_Stream extends PRESS_TUBE_Widget {

  private $channelId;

  private $embedDomain;

  private $livechat = 'https://www.youtube.com/live_chat?v=%s&embed_domain=%s';

  public $options = array(
		'title' => array(
			'label' => 'Title:',
			'type' => 'text',
			'default' => 'Live Stream'
		),
		'id' => array(
			'label' => 'Video Id:',
			'type' => 'text',
			'default' => null
		),
    'show_chat' => array(
			'label' => 'Show chat:',
			'type' => 'checkbox',
			'default' => '0'
		)
  );

  /**
	 * Sets up the widgets name etc
	 */
	public function __construct() {

    $this->channelId =  PRESS_TUBE_Settings::get_option( 'main', 'channelId' );

    $this->embedDomain = $_SERVER['SERVER_NAME'];

    $widget_ops = array(
			'classname' => 'wt_live_stream_widget',
			'description' => __( 'Display a live stream from YouTube.', 'press-tube' ),
		);

		parent::__construct( 'wt_live_stream_widget', 'Youtube Live Stream', $widget_ops );

	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {

    $instance = $this->ensure_defaults($instance);

    $output = $args['before_widget'];

    $output .= '<div class="press-tube-widget livestream livestream-container">';

		$output .= $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];

    // Exit if no video id don't waste api hits
    if( !empty($instance['id']) && PRESS_TUBE_Utils::validate_video_id($instance['id']) ) {

      $output .= '<div class="livestream-frame">';

        $output .= sprintf(
          '<div class="frame-wrapper"><iframe width="560" height="315" src="https://www.youtube.com/embed/%s" frameborder="0" allowfullscreen></iframe></div>',
          $instance['id']
        );

      $output .= '</div>';

      if( $instance['show_chat'] ) {

        $output .= '<div class="livestream-chat">';

          $livechaturl = sprintf( $this->livechat, $instance['id'], $this->embedDomain );

          $output .= sprintf(
            '<iframe width="560" height="315" src="%s" frameborder="0" allowfullscreen></iframe>', $livechaturl
          );

        $output .= '</div>';

      }

    } else {

      if( current_user_can('customize') ) {
        $output .= '<p>' . __( 'The Video Id is not valid.', 'press-tube' ) . '</p>';
      }

    }

    $output .= '</div>';

    $output .= $args['after_widget'];

    print $output;

	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {

		foreach ($this->options as $option_name => $option_details) {

			// Ensure that property exists with default value if not set
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
