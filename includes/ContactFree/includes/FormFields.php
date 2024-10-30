<?php
namespace ChopChop\ContactFormFree;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'FormFields' ) ) {

	class FormFields {

		/**
		 * Setup class
		 *
		 * @param array $field
		 */
		function __construct( $field = array() ) {
			if ( empty( $field ) || !$field['id'] ) {
				return;
			}

			$this->field = $field;
		}

		/**
		 * Call field build function for specific field type.
		 */
		public function get_field() {
			$type = $this->type;
			if ( ! $type ) {
				return;
			}

			$field_function = 'build_field_' . $this->type;

			if ( method_exists( $this, $field_function ) ) {
				echo $this->$field_function();
			}
		}

		/**
		 * Builds field name from repeater id, field name and row number.
		 *
		 * @return string
		 */
		private function get_name() {
			$name = '';

			return $name;
		}

		/**
		 * Builds field name from repeater id, field name and row number.
		 *
		 * @return string
		 */
		private function get_id() {
			return esc_html( $this->id );
		}

		/**
		 * Builds input label
		 *
		 * @param string $for
		 * @param string $label
		 *
		 * @return string
		 */
		private function get_label() {
			$html = '';
			if ($this->field['type'] == 'checkbox' || $this->field['type'] == 'radio' || $this->field['type'] == 'select') {
				$html = $this->label ? '<p class="chch-contact-free__label">' . $this->label . '</p>' : '';
			}
			else {
				$html = $this->label ? '<label class="chch-contact-free__label" for="' . $this->get_id() . '">' . $this->label . '</label>' : '';
			}

			return $html;
		}

		/**
		 * @param       $field_attrs
		 * @param array $attr_excluded
		 *
		 * @return string
		 */
		public function build_field_attrs( $field_attrs, $attr_excluded = array() ) {
			$attrs = '';
			foreach ( $field_attrs as $attr => $val ) {
				if ( in_array( $attr, ( array ) $attr_excluded ) || $val == '' ) {
					continue;
				}

				if ( is_array( $val ) ) {
					$attrs .= sprintf( " %s='%s'", $attr, json_encode( $val ) );
				} else {
					$attrs .= sprintf( ' %s="%s"', $attr, $val );
				}
			}

			return $attrs;
		}

		/**
		 * Magic getter for this object.
		 *
		 * @param $name
		 *
		 * @return string
		 */
		function __get( $name ) {
			if ( isset( $this->field[ $name ] ) ) {
				return $this->field[ $name ];
			} else {
				return '';
			}
		}

		/**
		 * Builds input type fields
		 *
		 * @param array $args
		 *
		 * @return string
		 */
		function build_field_input( $args = array() ) {
			$field_params = wp_parse_args( $args, array(
				'type'        => 'text',
				'name'        => $this->get_id(),
				'id'          => $this->get_id(),
				'class'       => 'chch-contact-free__input',
				'placeholder' => $this->placeholder,
				'data-req'    => $this->req,
				'data-type'   => $this->type,
			) );

			if(is_admin()){
				$field_params['value'] = $field_params['placeholder'];
				unset($field_params['placeholder']);
			}


			return sprintf( "%s<input %s>", $this->get_label(), $this->build_field_attrs( $field_params ) );
		}

		/**
		 * Builds text fields
		 *
		 * @param array $args
		 *
		 * @return string
		 */
		private function build_field_text( $args = array() ) {
			return $this->build_field_input();
		}

		/**
		 * Builds email fields
		 *
		 * @param array $args
		 *
		 * @return string
		 */
		private function build_field_email( $args = array() ) {
			return $this->build_field_input(   );
		}

		/**
		 * Builds phone fields
		 *
		 * @param array $args
		 *
		 * @return string
		 */
		private function build_field_phone( $args = array() ) {
			return $this->build_field_input( );
		}

		/**
		 * Builds date fields
		 *
		 * @param array $args
		 *
		 * @return string
		 */
		private function build_field_date( $args = array() ) {
			return $this->build_field_input( array(
				'type' => 'date',
				'placeholder' => $this->dateformat
			) );
		}

		/**
		 * Builds date birthday
		 *
		 * @param array $args
		 *
		 * @return string
		 */
		private function build_field_birthday( $args = array() ) {
			return $this->build_field_input( array(
				'type' => 'date',
				'placeholder' => $this->dateformat
			) );
		}

		/**
		 * Builds number fields
		 *
		 * @param array $args
		 *
		 * @return string
		 */
		private function build_field_number( $args = array() ) {
			return $this->build_field_input( array(
				'type' => 'number',
			) );
		}

		/**
		 * Builds url fields
		 *
		 * @param array $args
		 *
		 * @return string
		 */
		private function build_field_url( $args = array() ) {
			return $this->build_field_input(   );
		}

		/**
		 * Builds zip fields
		 *
		 * @param array $args
		 *
		 * @return string
		 */
		private function build_field_zip( $args = array() ) {
			return $this->build_field_input();
		}

		/**
		 * Builds checkbox fields
		 *
		 * @param array $args
		 *
		 * @return string
		 */
		private function build_field_checkbox( $args = array() ) {
			$field_params = wp_parse_args( $args, array(
				'name'    => $this->id,
				'id'      => $this->id,
				'class'   => 'chch-contact-free__field',
				'checkbox_options' => $this->checkbox_options,
				'data-type'   => $this->type,
			) );

			return sprintf( "<div %s>%s%s</div>", $this->build_field_attrs( $field_params, array( 'checkbox_options' ) ), $this->get_label(), $this->build_checkboxes( $field_params[ 'checkbox_options' ] ) );
		}

		/**
		 * Builds radio fields
		 *
		 * @param array $args
		 *
		 * @return string
		 */
		private function build_field_radio( $args = array() ) {
			$field_params = wp_parse_args( $args, array(
				'name'    => $this->get_id(),
				'id'      => $this->get_id(),
				'class'   => 'chch-contact-free__field',
				'options' => $this->options,
				'data-type'   => $this->type,
			) );

			return sprintf( "<div %s>%s%s</div>", $this->build_field_attrs( $field_params, array( 'options' ) ), $this->get_label(), $this->build_radio_buttons( $field_params[ 'options' ] ) );
		}

		/**
		 * Builds select fields
		 *
		 * @param array $args
		 *
		 * @return string
		 */
		function build_field_select( $args = array() ) {
			$field_params = wp_parse_args( $args, array(
				'name'    => $this->get_id(),
				'id'      => $this->get_id(),
				'class'   => 'chch-contact-free__input',
				'options' => $this->options,
				'data-type'   => $this->type,
			) );

			return sprintf( "%s<select %s>%s</select>", $this->get_label(), $this->build_field_attrs( $field_params, array( 'options' ) ), $this->build_select_options( $field_params[ 'options' ] ) );
		}

		/**
		 * Builds select fields
		 *
		 * @param array $args
		 *
		 * @return string
		 */
		function build_field_dropdown( $args = array() ) {
			$field_params = wp_parse_args( $args, array(
				'name'    => $this->get_id(),
				'id'      => $this->get_id(),
				'class'   => 'chch-contact-free__input',
				'options' => $this->options,
				'data-type'   => $this->type,
			) );

			return sprintf( "%s<select %s>%s</select>", $this->get_label(), $this->build_field_attrs( $field_params, array( 'options' ) ), $this->build_select_options( $field_params[ 'options' ] ) );
		}

		/**
		 * Builds select options
		 *
		 * @param $select_options
		 *
		 * @return string
		 *
		 */
		function build_select_options( $select_options ) {
			if ( ! is_array( $select_options ) ) {
				return '';
			}
			$options = '';
			foreach ( $select_options as $key => $val ) {
				$selected = ''; 

    			if (isset($this->field['selected']) && $this->field['selected'] == array_search($key, array_keys($select_options))) {
    			    $selected = ' selected';
    			}
				$options .= sprintf( "\t".'<option value="%1$s"%2$s>%1$s</option>'."\n", $val['value'], $selected );

			}

			return $options;
		}

		/**
		 * Builds individual checkboxes
		 *
		 * @param $checkboxes
		 *
		 * @return string
		 */
		function build_checkboxes( $checkboxes ) {
			if ( ! is_array( $checkboxes ) ) {
				return '';
			}
			$options = '';
			foreach ( $checkboxes as $key => $val ) {
				$index = array_search( $key, array_keys( $checkboxes ));
				$checked = ( isset($val['selected']) && $val['selected'] == 'yes' ) ? 'checked' : '';
				if (isset($val['value']) && $val['value'] !='') {
					$options .= sprintf( '<label for="%1$s"><input type="checkbox" name="%2$s" id="%1$s" value="%3$s" class="chch-contact-free__checkbox" %4$s data-type="%5$s"><span class="chch-contact-free__label">%3$s</span></label>', $this->id.'-'.$index, $this->id, $val['value'], $checked, $this->type );
				}
			}

			return $options;
		}

		/**
		 * Builds individual radio buttons
		 *
		 * @param $radios
		 *
		 * @return string
		 *
		 */
		function build_radio_buttons( $radios ) {
			if ( ! is_array( $radios ) ) {
				return '';
			}
			$options = '';
			foreach ( $radios as $key => $val ) {
				$checked = '';
				$index = array_search( $key, array_keys( $radios ));
				if ( isset( $this->field[ 'selected' ] ) && $this->field[ 'selected' ] == $index ) {
					$checked = 'checked';
				}
				if (isset($val['value']) && $val['value'] !='') {
					$options .= sprintf( '<label for="%1$s"><input type="radio" name="%2$s" id="%1$s" value="%3$s" %4$s class="chch-contact-free__radio" data-type="%5$s"><span class="chch-contact-free__label">%3$s</span></label>', $this->id.'-'.$index, $this->id, $val[ 'value' ], $checked, $this->type );
				}
			}

			return $options;
		}

		/**
		 * Builds textarea fields
		 *
		 * @param array $args
		 *
		 * @return string
		 */
		function build_field_textarea( $args = array() ) {
			$field_params = wp_parse_args( $args, array(
				'name'        => $this->get_id(),
				'id'          => $this->get_id(),
				'class'       => 'chch-contact-free__input',
				'placeholder' => $this->placeholder,
				'data-type'   => $this->type,
			) );

			$val= is_admin() ? $this->placeholder : '';

			return sprintf( "%s<textarea %s>%s</textarea>", $this->get_label( $this->id, $this->label ), $this->build_field_attrs( $field_params ),$val );

		}

		/**
		 * Builds address fields
		 *
		 * @param array $args
		 *
		 * @return string
		 */
		function build_field_address( $args = array() ) {
			$field_params = wp_parse_args( $args, array(
				'name'        => $this->get_id(),
				'id'          => $this->get_id(),
				'class'       => '',
				'placeholder' => $this->placeholder,
				'data-type'   => $this->type,
			) );
			$val= is_admin() ? $this->placeholder : '';

			return sprintf( "%s<textarea %s>%s</textarea>", $this->get_label( $this->id, $this->label ), $this->build_field_attrs( $field_params ),$val );

		}

	}
}