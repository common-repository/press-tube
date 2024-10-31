<?php

// Block direct access to file
defined( 'ABSPATH' ) or die( 'Not Authorized!' );

/**
 * The abstract option class with common methods
 */
abstract class PRESS_TUBE_Option {

  /**
   * The unique ID for the option that is not prefixed
   * @var string
   */
  protected $option_id;

  /**
   * The prefixed option name unique ID
   * @var string
   */
  protected $option_name;

  /**
   * The option group
   * @var string
   */
  public $group_name;

  /**
   * The settings section title
   * @var string
   */
  public $section_title;

  /**
   * Optional settings field array to create setting section
   * @var array
   */
  protected $settings_fields = array();

  /**
   * The current options values
   * @var array
   */
  protected $options;

  /**
   * The class istance object
   */
  protected static $instance;

  protected function __construct() {

    // Build the option group if not exists
    if( empty($this->group_name) ) {
      $this->group_name = 'press-tube_' . $this->option_name . '_options';
    }

    // Build the option name
    $this->option_name = 'press-tube_' . $this->option_name;

    // Get the options and store in the class
    $this->options = get_option( $this->option_name );

    // If settings exists ensure no zombie properties exist
    if( $this->options && is_array($this->options) ) {
      update_option( $this->option_name, array_intersect_key( $this->options, $this->settings_fields ) );
    }

    // Register settings
    add_action( 'admin_init', array( $this, 'register_settings' ) );

  }

  /**
   * Abstract sanitize function for plugin settings
   * @method sanitize_callback
   */
  abstract protected function sanitize_callback($input);

  /**
   * Register the current option into settings
   * by creating (if necessary) option group
   * @method register_settings
   */
  public function register_settings() {

    register_setting( $this->group_name, $this->option_name, array($this, 'sanitize_callback') );

    // create the settings section
    if( $this->section_title && $this->settings_fields && is_array($this->settings_fields) ) {

      // Add the main settings section with description
      add_settings_section( $this->option_name, __( $this->section_title, 'press-tube' ), null, PRESS_TUBE_Settings::$page_id );

      // Add all settings fields
      foreach ($this->settings_fields as $field_id => $field_data) {

        add_settings_field( $field_id, $field_data['title'], array( $this, 'settings_field_callback' ), PRESS_TUBE_Settings::$page_id, $this->option_name, array(
          'option_name' => $field_id,
          'option_values' => $field_data
        ) );

      }

    }

  }

  /**
   *
   * // TODO: do this part better with some rendering function like wp-tools
   * Print the settings field element
   * @param  array $option_args The option details
   */
  public function settings_field_callback($option_args) {

    // store option values
    $values = $option_args['option_values'];
    $name = $option_args['option_name'];

    // The option input name
    $values['name'] = $this->option_name . '[' . $name . ']';

    // Get the value or set to empty, maybe default value if needed
    $values['value'] = isset($this->options[$name]) && !empty($this->options[$name]) ? esc_attr( $this->options[$name]) : '';

    // The input description (optional)
    $values['description'] = isset($values['description']) ? $values['description'] : '';

    $output = str_replace(
			array_map(function($key){ return '{' . $key . '}'; }, array_keys($values)),
			array_values(array_map('esc_attr', $values)),
			'<input type="{type}" id="{name}"
      class="widefat" name="{name}"
      value="{value}" {checked} />'
		);

    // Add description that can contain html text
    if( $values['description'] ) {
      $output .= sprintf('<p class="description">%s</p>', $values['description']);
    }

    // output the option
    print $output;

  }

  /**
   * Get a single option by name from the group
   * @method get_option
   */
  public function get_option($option_name) {

    if( isset($this->options[$option_name]) ) {
      return $this->options[$option_name];
    }

  }

  /**
   * Get the full options array
   * @method get_options
   */
  public function get_options() {
    return $this->options;
  }

  /**
   * Get the prefixed option name
   * @method get_option_name
   * @return string The prefixed option name
   */
  public function get_option_name() {
    return $this->option_name;
  }

  /**
   * Get the option group name
   * @method get_option_group
   * @return string The option group name
   */
  public function get_option_group() {
    return $this->group_name;
  }

  /**
   * Clear the options values
   * @method clear_options
   * @return boolean True if the option was cleared
   */
  public function clear_options() {
    return update_option( $this->option_name, false );
  }

  /**
   * Update a single option value by key
   * @method update_option
   */
  public function update_option($option_name, $option_value) {

    $this->options[$option_name] = $option_value;
    return update_option( $this->option_name, $this->options );

  }

  /**
   * Update all the options at once
   * @method update_options
   */
  public function update_options($option_value) {
    $this->options = $option_value;
    return update_option( $this->option_name, $this->options );
  }

}
