<?php

// Block direct access to file
defined( 'ABSPATH' ) or die( 'Not Authorized!' );

require_once( PRESS_TUBE_WIDGETS . 'class-widget.php' );

class PRESS_TUBE_Subscribe extends PRESS_TUBE_Widget {

	/**
	 * The widget options displayed in backend
	 * @var array
	 */
	public $options = array(
		'title' => array(
			'label' => 'Title:',
			'type' => 'text',
			'default' => 'Subscribe'
		),
		'channelId' => array(
			'label' => 'Channel Id:',
			'type' => 'text',
			'default' => null
		),
    'layout' => array(
			'label' => 'Layout:',
			'type' => 'select',
			'default' => 'default',
			'options' => array(
				'default' => 'Default',
				'full' => 'Full',
			)
		),
    'theme' => array(
			'label' => 'Theme:',
			'type' => 'select',
			'default' => 'default',
			'options' => array(
				'default' => 'Default',
				'dark' => 'Dark',
			)
		),
    'count' => array(
			'label' => 'Subscriber count:',
			'type' => 'select',
			'default' => 'default',
			'options' => array(
				'default' => 'Default',
				'hidden' => 'Hidden',
			)
		),
	);

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {

		$widget_ops = array(
			'classname' => 'press_tube_subscribe_widget',
			'description' => __( 'Display videos list from YouTube with many options.', 'press-tube' ),
		);

		parent::__construct( 'press_tube_subscribe_widget', 'Youtube Subscribe', $widget_ops );

    $this->options['channelId']['default'] = PRESS_TUBE_Settings::get_option( 'main', 'channelId' );

	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {

    $instance = $this->ensure_defaults($instance);

		// Get or set the default widget title
		$instance['title'] = empty($instance['title']) ? __('Subscribe', 'press-tube') : $instance['title'];

		print( $args['before_widget'] );

		print( $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'] );

    if( PRESS_TUBE_Utils::validate_channel($instance['channelId']) ) {

      printf(
        '<div class="g-ytsubscribe" data-channelid="%s" data-layout="%s" data-theme="%s" data-count="%s"></div>',
        $instance['channelId'],
        $instance['layout'],
        $instance['theme'],
        $instance['count']
      );

    } else {

      if( current_user_can('customize') ) {

        // Alert only editor users
        _e( 'The Channel Id is not valid.', 'press-tube' );

      }

    }

		print( $args['after_widget'] );

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
