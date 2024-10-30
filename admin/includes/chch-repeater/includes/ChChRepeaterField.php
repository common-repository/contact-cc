<?php
namespace ChopChop\Repeater;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'ChChRepeaterView' ) ) {

	/**
	 * @property array field
	 * @property  row_number
	 * @property array saved_value
	 */
	class ChChRepeaterField {
		private $field_num;

		private $field;

		private $saved_value;
		/**
		 * @type array
		 */
		private $parent_data;

		/**
		 * Setup class
		 *
		 * @param array $field
		 * @param int   $field_num
		 * @param       $repeater_id
		 * @param array $saved_value
		 * @param array $parent_data
		 */
		function __construct( $field = array(), $field_num = 0, $repeater_id = 'repeater', $saved_value = array(), $parent_data = array() ) {

			if ( empty( $field ) ) {
				return;
			}

			$this->field       = $field;
			$this->field_num   = $field_num;
			$this->repeater_id = $repeater_id;
			$this->saved_value = $saved_value;
			$this->parent_data = $parent_data;
		}

		/**
		 * Call field build function for specific field type.
		 */
		public function get_field() {
			if ( ! isset( $this->field[ 'type' ] ) ) {
				return;
			}

			$field_function = 'build_field_' . $this->field[ 'type' ];

			if ( method_exists( $this, $field_function ) ) {
				$show_if       = 'data-show-if="%s"';
				$field_show_if = $this->show_if ? $this->show_if : 'any';
				$show_if       = sprintf( $show_if, $field_show_if );
				$repeater      = ( $this->type === 'repeater' ) ? 'chch-repeater' : '';
				printf( "<div class=\"chch-repeater-field-wrapper chch-repeater-%s-wrapper %s\" %s>", $this->field[ 'type' ], $repeater, $show_if );
				if ( $this->name ) {
					printf( "<label >%s</label>", $this->name );
				}
				echo $this->$field_function();
				echo '</div>';
			}
		}

		/**
		 * Builds field name from repeater id, field name and row number.
		 *
		 * @param bool $group_rows
		 *
		 * @return string
		 */
		private function get_name( $group_rows = false ) {
			if ( ! empty( $this->parent_data ) ) {
				$name = $this->get_repeater_field_name( $group_rows );
			} else {
				$name = '_' . $this->repeater_id . '[' . $this->field_num . ']' . '[' . $this->__get( 'id' ) . ']';
			}

			return $name;
		}

		/**
		 * Builds field name from repeater id, field name and row number.
		 *
		 * @param bool $group_rows
		 *
		 * @return string
		 */
		private function get_repeater_field_name( $group_rows = false ) {
			$name = '_' . $this->repeater_id . '[' . $this->parent_data[ 'row_num' ] . ']' . ( $group_rows ? '' :
					( '[' . $this->parent_data[ 'id' ] . '][' . $this->field_num . ']' ) ) . '[' . $this->id . ']';

			return $name;
		}

		/**
		 * Builds field name from repeater id, field name and row number.
		 *
		 * @return string
		 */
		private function get_id() {
			$field_id = '-' . str_replace( '_', '-', $this->repeater_id ) . '-' . $this->id;

			return $field_id;
		}

		/**
		 * Returns field description.
		 *
		 * @return string
		 */
		private function get_desc() {
			$description = '';
			if ( $this->desc ) {
				$description = sprintf( '<span class="cmb_metabox_description">%s</span>', $this->desc );
			}

			return $description;
		}

		/**
		 * Get field value from saved data or default value.
		 *
		 * @return string
		 */
		private function get_value() {
			if ( ! empty( $this->saved_value ) ) {
				if ( isset( $this->saved_value[ $this->id ] ) ) {
					return $this->saved_value[ $this->id ];
				}
			} else {
				return $this->default;
			}
		}

		/**
		 * Get checkbox value.
		 *
		 * @return string - 'checked' or empty string.
		 */
		function get_checkbox_vaule() {

			$checked = '';
			if ( ! empty( $this->saved_value ) ) {
				if ( isset( $this->saved_value[ $this->id ] ) ) {
					$checked = 'checked';
				}
			} elseif ( $this->default == 'checked' ) {
				$checked = 'checked';
			}

			return $checked;
		}

		/**
		 * Get radio value.
		 *
		 * @return string - 'checked' or empty string.
		 */
		function get_radio_vaule() {
			$checked = '';
			if ( !empty( $this->saved_value ) ) {
				if ( isset($this->field['group_rows']) && $this->field['group_rows'] && isset($this->parent_data[$this->id]) && $this->field_num == $this->parent_data[$this->id] ) {
					$checked = 'checked';
				}
			} elseif ( $this->__get( 'default' ) == 'checked' ) {
				$checked = 'checked';
			}

			return $checked;
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
				'type'  => 'text',
				'name'  => $this->get_name(),
				'id'    => $this->get_id(),
				'class' => 'chch-repeater-text chch-repeater-field',
				'value' => $this->get_value(),
			) );

			return sprintf( "<input %s>%s", $this->build_field_attrs( $field_params ), $this->get_desc() );
		}

		/**
		 * Builds function for text type fields
		 *
		 * @param array $args
		 *
		 * @return string
		 */
		private function build_field_text( $args = array() ) {
			return $this->build_field_input();
		}

		function build_field_checkbox( $args = array() ) {
			$field_params = wp_parse_args( $args, array(
				'name'  => $this->get_name(),
				'id'    => $this->get_id(),
				'value' => 'yes',
				'class' => 'chch-repeater-checkbox chch-repeater-field',
			) );
			
			return sprintf( "<input type=\"checkbox\" %s %s>%s", $this->build_field_attrs( $field_params ), $this->get_checkbox_vaule(), $this->get_desc() );
		}

		function build_field_radio( $args = array() ) {
			$field_params = wp_parse_args( $args, array(
				'name'                  => $this->get_name($this->group_rows),
				'id'                    => $this->get_id(),
				'value'                 => strval($this->field_num),
				'data-group_rows'       => $this->group_rows ? 'true' : 'false',
				'class'=> 'chch-repeater-checkbox chch-repeater-field',
			) );

			return sprintf( "<input type=\"radio\" %s %s>%s", $this->build_field_attrs( $field_params ), $this->get_radio_vaule(), $this->get_desc() );

		}

		function build_field_select( $args = array() ) {
			$field_params = wp_parse_args( $args, array(
				'name'    => $this->get_name(),
				'id'      => $this->get_id(),
				'class'   => 'chch-lp-customize-style chch-lp-to-trigger',
				'options' => $this->options,
			) );

			return sprintf( "<select %s>%s</select>%s ", $this->build_field_attrs( $field_params, array( 'options' ) ), $this->build_select_options( $field_params[ 'options' ] ), $this->get_desc() );
		}

		function build_select_options( $select_options ) {
			if ( ! is_array( $select_options ) ) {
				return '';
			}
			$value   = $this->get_value();
			$options = '';
			foreach ( $select_options as $key => $val ) {
				$selected = ( $key == $value ) ? 'selected' : '';
				$options .= sprintf( "\t<option value=\"%s\" %s>%s</option>\n", $key, $selected, $val );
			}

			return $options;
		}

		function build_field_revealer_select( $args = array() ) {
			$field_params = wp_parse_args( $args, array(
				'class' => ' chch-repeater-revealer chch-repeater-select chch-repeater-field',
			) );

			return $this->build_field_select( $field_params );
		}

		function build_field_repeater( $args = array() ) {

			$j = 0;

			$parent_data = array(
				'id'      => $this->id,
				'row_num' => $this->field_num,
			);

			$fields = $this->field[ 'fields' ];

			foreach ( $fields as $field ) {
				if ( isset( $field[ 'group_rows' ] ) && $field[ 'group_rows' ] && isset( $this->saved_value[ $field[ 'id' ] ] ) ) {
					$parent_data[ $field[ 'id' ] ] = $this->saved_value[ $field[ 'id' ] ];
				}
			}

			$saved_fields = $this->saved_value;

			echo "<div class=\"chch-repeater-row-wrapper\">\n";

			if ( ! empty( $saved_fields ) && isset( $saved_fields[ $this->id ] ) ) {
				foreach ( $saved_fields[ $this->id ] as $fields ) {

					ChChRepeaterView::get_row( $j, $this->fields, $this->repeater_id, $fields, $parent_data );
					$j ++;
				}
			} else {
				ChChRepeaterView::get_row( $j, $this->fields, $this->repeater_id, '', $parent_data );
			}

			echo "</div>\n";
			echo '<a class="chch-repeater-add-row button button-primary" href="#" data-row-count=' . $j . ' data-id="' . $this->get_name() . '">' . __( 'Add Field', 'ChChRepeater' ) . '</a>';
			echo $this->get_desc();
		}

		function build_field_textarea( $args = array() ) {
			$field_params = wp_parse_args( $args, array(
				'name'  => $this->get_name(),
				'id'    => $this->get_id(),
				'class' => 'chch-lp-customize-content',
				'desc'  => $this->get_desc(),
				'value' => $this->get_value(),
			) );

			return sprintf( "%s<textarea %s>%s</textarea>", $field_params[ 'desc' ], $this->build_field_attrs( $field_params, array( 'desc', 'value' ) ), $field_params[ 'value' ] );
		}

	}
}