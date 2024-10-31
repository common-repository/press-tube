<?php

// Block direct access to file
defined( 'ABSPATH' ) or die( 'Not Authorized!' );

/**
 * Commons methods for widget form rendering and stuff
 * maybe this can be done diffent we will see in future
 */
class PRESS_TUBE_Widget extends WP_Widget {

  public function print_checkbox_input($name, $details, $value) {

		$field_id = esc_attr( $this->get_field_id( $name ) );
		$field_name = esc_attr( $this->get_field_name( $name ) );
		$field_checked = checked( $value, true, false );

		printf(
			'<p>
	      <label for="%s">%s</label>
	      <input name="%s" type="hidden" value="0">
	      <input class="widefat" id="%s" name="%s" type="%s" value="1" %s>
	    </p>',
			$field_id, __($details['label'], 'press-tube'), $field_name, $field_id, $field_name, $details['type'], $field_checked

		);

	}

	public function print_standard_input($name, $details, $value) {

		$field_id = esc_attr( $this->get_field_id( $name ) );
		$field_name = esc_attr( $this->get_field_name( $name ) );

		printf(
			'<p>
	      <label for="%s">%s</label>
	      <input class="widefat" id="%s" name="%s" type="%s" value="%s">
	    </p>',
			$field_id, __($details['label'], 'press-tube'), $field_id, $field_name, $details['type'], $value

		);

	}

	public function print_select_input($name, $details, $value) {

		$field_id = esc_attr( $this->get_field_id( $name ) );
		$field_name = esc_attr( $this->get_field_name( $name ) );

		$output = sprintf('<p><label for="%s">%s</label>', $field_id, $details['label']);
		$output .= sprintf('<select class="widefat" id="%s" name="%s" value="%s">', $field_id, $field_name, $value);

		foreach ($details['options'] as $option_value => $option_name) {
			$selected = $value === $option_value ? 'selected' : '';
			$output .= sprintf('<option value="%s" %s>%s</option>', $option_value, $selected, $option_name);
		}

		$output .= sprintf('</select></p>');

		print $output;

	}

  public function ensure_defaults($instance) {

    foreach ($this->options as $option_name => $option_values) {
      $instance[$option_name] = isset($instance[$option_name]) ? $instance[$option_name] : $option_values['default'];
    }

    return $instance;

  }

}
