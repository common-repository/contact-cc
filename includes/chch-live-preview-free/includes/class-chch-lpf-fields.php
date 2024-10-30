<?php
namespace ChopChop\LivePreviewFree;

if ( ! class_exists( 'ChopChop\\LivePreviewFree\\CHCH_LPF_Fields' ) ) {
	class CHCH_LPF_Fields {

		/**
		 * Holds all field params
		 *
		 * @param array
		 */
		private $field = array();

		private $disabled_defaults = array();

		private $template = 'template';

		private $option_group = '';

		/**
		 * LP_Fields::__construct()
		 *
		 * @param mixed  $field
		 * @param mixed  $option_group
		 * @param string $template
		 *
		 */
		public function __construct( $field, $option_group, $template = 'template', CHCH_LPF $lp, $id, $disabled = false ) {
			$this->lp           = $lp;
			$this->field        = $field;
			$this->option_group = $option_group;
			$this->template     = $template;
			$this->id           = $id;
			$this->disabled     = $disabled;
		}

		/**
		 * Check field type and call a proper build function
		 *
		 * @return string - field with all attrs
		 */
		function get_field() {

			$field_type = isset( $this->field['type'] ) ? $this->field['type'] : 'text';

			if ( method_exists( $this, 'build_field_' . $field_type ) ) {
				$field_fun = 'build_field_' . $field_type;
				do_action( 'lp_before_field_' . $field_type );

				return $this->$field_fun();
				do_action( 'lp_after_field_' . $field_type );
			}
		}

		/**
		 * LP_Fields::build_field_attrs()
		 *
		 * @param mixed $field_attrs
		 * @param mixed $attrs_excluded
		 *
		 * @return string $attrs
		 */
		public function build_field_attrs( $field_attrs, $attrs_excluded = array() ) {
			$attrs = '';
			foreach ( $field_attrs as $attr => $val ) {
				if ( in_array( $attr, ( array ) $attrs_excluded ) || $val == '' ) {
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
		 * LP_Fields::build_field_input()
		 *
		 * @param mixed $args
		 *
		 * @return string
		 */
		function build_field_input( $args = array() ) {
			$field_params = wp_parse_args( $args, array(
				'type'                  => 'text',
				'name'                  => $this->get_name(),
				'id'                    => $this->get_id(),
				'class'                 => 'chch-lpf-customize-content',
				'data-template'         => $this->template,
				'data-customize-target' => $this->get_field_param( 'target' ),
				'data-attr'             => $this->get_field_param( 'attr' ),
				'data-unit'             => $this->get_field_param( 'unit' ),
				'value'                 => $this->get_value(),
				'data-add-css'          => $this->get_field_param( 'add_css' ),
			) );

			return sprintf( "%s<input %s>", $this->get_desc(), $this->build_field_attrs( $field_params ) );
		}

		/**
		 * LP_Fields::build_field_text()
		 *
		 * @param mixed $args
		 *
		 * @return
		 */
		function build_field_text( $args = array() ) {

			return $this->build_field_input();
		}

		/**
		 * LP_Fields::build_field_text()
		 *
		 * @param mixed $args
		 *
		 * @return string
		 */
		function build_field_number( $args = array() ) {

			return $this->build_field_input( array( 'type' => 'number', 'class' => 'chch-lpf-customize-style', ) );
		}

		/**
		 * LP_Fields::build_field_text()
		 *
		 * @param mixed $args
		 *
		 * @return
		 */
		function build_field_attr( $args = array() ) {
			return $this->build_field_input( array( 'class' => 'chch-lpf-attr' ) );
		}

		function build_field_upload( $args = array() ) {
			return $this->build_field_input( array(
				'id'    => $this->get_id( 'upload-input' ),
				'class' => 'chch-lpf-customize-style',
			) ) . $this->build_field_input( array(
				'type'        => 'button',
				'class'       => 'chch-lpf-upload button',
				'value'       => __( 'Upload Image' ),
				'data-target' => $this->get_id( 'upload-input' ),
			) );
		}

		/**
		 * LP_Fields::build_field_checkbox()
		 *
		 * @param mixed $args
		 *
		 * @return
		 */
		function build_field_checkbox( $args = array() ) {
			$field_params = wp_parse_args( $args, array(
				'name'                  => $this->get_name(),
				'id'                    => $this->get_id(),
				'data-template'         => $this->template,
				'data-customize-target' => $this->get_field_param( 'target' ),
				'value'                 => 'on',
			) );

			return sprintf( "%s<input type=\"checkbox\" %s %s>", $this->get_desc(), $this->build_field_attrs( $field_params ), $this->get_checkbox_vaule() );
		}

		/**
		 * LP_Fields::build_field_remover_checkbox()
		 *
		 * @param mixed $args
		 *
		 * @return
		 */
		function build_field_remover_checkbox( $args = array() ) {

			return $this->build_field_checkbox( array( 'class' => 'remover-checkbox chch-lpf-to-trigger', ) );
		}

		/**
		 * LP_Fields::build_field_color_picker()
		 *
		 * @param mixed $args
		 *
		 * @return
		 */
		function build_field_color_picker( $args = array() ) {
			$color_picker_params = array(
				'type'  => 'text',
				'class' => 'chch-lpf-colorpicker chch-lpf-customize-style chch-lpf-to-trigger',
			);
			$disabled_params     = array(
				'type'  => 'text',
				'class' => 'chch-lpf-colorpicker-disabled chch-lpf-disabled ',
				'value' => '#fff',
			);

			$field_param = $this->disabled ? $disabled_params : $color_picker_params;

			return $this->build_field_input( $field_param );
		}

		/**
		 * LP_Fields::build_field_slider()
		 *
		 * @param mixed $args
		 *
		 * @return
		 */
		function build_field_slider( $args = array() ) {

			$slider          = array(
				'type'  => 'hidden',
				'class' => 'chch-lpf-slider chch-lpf-customize-style',
			);
			$disabled_params = array(
				'type'  => 'hidden',
				'class' => '',
			);

			$field_param = $this->disabled ? $disabled_params : $slider;

			$slider=  $this->build_field_input( $field_param );

			$slider .= "<script type=\"text/javascript\">\n\tjQuery(document).ready( function ($) {";
			$slider .= sprintf( "\t$( \"#%s\" ).slider({\n", $this->get_id( 'slider' ) );
			$slider .= sprintf( "\t\tmax: %s,\n", $this->disabled ? '1' : $this->get_field_param( 'max' ) );
			$slider .= sprintf( "\t\tmin: %s,\n", $this->disabled ? '1' : $this->get_field_param( 'min' ) );
			$slider .= sprintf( "\t\tvalue: %s,\n", $this->disabled ? '0' : $this->get_value() );
			$slider .= sprintf( "\t\tstep: %s,\n", $this->disabled ? '0.1' : $this->get_field_param( 'step' ) );
			$slider .= '});});</script>';
			$slider .= sprintf( "\t<div id='%s' class='%s' data-target=\"%s\"></div> \n", $this->get_id( 'slider' ), $this->disabled ? '': 'chch-lpf-slider', $this->get_name() );

			return $slider;
		}

		/**
		 * LP_Fields::build_field_select()
		 *
		 * @param mixed $args
		 *
		 * @return
		 */
		function build_field_select( $args = array() ) {
			$field_params = wp_parse_args( $args, array(
				'name'                  => $this->get_name(),
				'id'                    => $this->get_id(),
				'class'                 => 'chch-lpf-customize-style chch-lpf-to-trigger',
				'data-template'         => $this->template,
				'data-customize-target' => $this->get_field_param( 'target' ),
				'data-attr'             => $this->get_field_param( 'attr' ),
				'options'               => $this->get_field_param( 'options' ),
				'data-add-css'          => $this->get_field_param( 'add_css' ),
			) );

			if ( $this->disabled ) {
				$field_params['class'] = 'chch-lpf-customize-disabled';
			}

			return sprintf( "%s <select %s>%s</select>", $this->get_desc(), $this->build_field_attrs( $field_params, array( 'options' ) ), $this->build_select_options( $field_params['options'] ) );
		}

		/**
		 * LP_Fields::build_field_class_switcher()
		 *
		 * @param mixed $args
		 *
		 * @return
		 */
		function build_field_class_switcher( $args = array() ) {
			return $this->build_field_select( array(
				'class'    => 'chch-lpf-class-switcher chch-lpf-to-trigger',
				'data-old' => $this->get_value(),
			) );
		}

		/**
		 * LP_Fields::build_select_options()
		 *
		 * @param mixed $select_options
		 *
		 * @return
		 */
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

		/**
		 * LP_Fields::build_field_input()
		 *
		 * @param mixed $args
		 *
		 * @return
		 */
		function build_field_textarea( $args = array() ) {
			$field_params = wp_parse_args( $args, array(
				'name'                  => $this->get_name(),
				'id'                    => $this->get_id(),
				'class'                 => 'chch-lpf-customize-content',
				'data-template'         => $this->template,
				'data-customize-target' => $this->field['target'],
				'desc'                  => $this->get_desc(),
			) );

			return sprintf( "%s<textarea %s>%s</textarea>", $field_params['desc'], $this->build_field_attrs( $field_params, array( 'desc' ) ), $this->get_value() );
		}

		/**
		 * LP_Fields::build_field_editor()
		 *
		 * @param mixed $args
		 *
		 * @return
		 */
		function build_field_editor( $args = array() ) {

			ob_start();
			echo $this->get_desc();
			echo '<div id="wp-' . $this->get_id() . '-wrap" class="wp-core-ui wp-editor-wrap tmce-active" >
            <div id="wp-' . $this->get_id() . '-media-buttons" class="wp-media-buttons">
              <a href="#" id="' . $this->get_id() . '-insert-media-button" class="button insert-media add_media" data-editor="' . $this->get_id() . '" title="Add Media"><span class="wp-media-buttons-icon"></span> Add Media</a>
            </div>
            
            <div id="wp-' . $this->get_id() . '-editor-container" class="wp-editor-container" ><div id="qt_' . $this->get_id() . '_toolbar" class="quicktags-toolbar"></div>
';
			echo $this->build_field_textarea( array(
				'id'    => $this->get_id(),
				'class' => 'chch-lpf-editor',
				'desc'  => '',
			) );

			echo '</div></div>';

			printf( "\n<div style=\"display:none;\">%s</div>\n", $this->build_field_textarea( array(
				'id'        => $this->get_id( 'lp-editor' ),
				'name'      => $this->get_name( 'lp-editor' ),
				'desc'      => '',
				'data-html' => $this->get_field_param( 'html' ),
			) ) );
			$lp_editor = ob_get_clean();

			return $lp_editor;
		}

		/**
		 * LP_Fields::build_field_editor()
		 *
		 * @param mixed $args
		 *
		 * @return
		 */
		function build_field_revealer_group( $args = array() ) {

			$background_field = '<label>';

			$background_field .= $this->build_field_select( array(
				'class'      => 'chch-lpf-revealer chch-lpf-to-trigger',
				'target'     => '',
				'data-group' => $this->get_id( 'group' ),
			) );

			$background_field .= '</label>';

			if ( $groups = $this->get_field_param( 'revaeals' ) ) {
				foreach ( $groups as $group ) {

					$hide = ( $this->get_value() == $group['section_id'] ) ? '' : 'hide-section disabled-sections';

					$background_field .= sprintf( "<div class=\"%s %s chch-lpf-bg\" id=\"chch-lpf-revealer-section-%s\">", $hide, $this->get_id( 'group' ), $group['section_id'] );

					if ( isset( $group['fields'] ) ) {
						foreach ( $group['fields'] as $field ) {
							$field_obj = new CHCH_LPF_Fields( $field, $this->option_group, $this->template, $this->lp, $this->id );
							$background_field .= $field_obj->get_field();
						}
					}

					$background_field .= '</div>';
				}
			}

			return $background_field;
		}

		/**
		 * LP_Fields::get_desc()
		 *
		 * @return
		 */
		private function get_desc() {
			if ( ! isset( $this->field['desc'] ) || empty( $this->field['desc'] ) ) {
				return '';
			}

			return sprintf( "<span class=\"customize-control-title\">%s</span>\n", $this->field['desc'] );
		}

		/**
		 * LP_Fields::get_name()
		 *
		 * @return
		 */
		public function get_name( $suffix = '' ) {
			$name = '_' . $this->template . '_' . $this->option_group . '_' . $this->get_field_param( 'name' );
			$name = $suffix ? $name . '_' . $suffix : $name;

			return $name;
		}

		/**
		 * LP_Fields::get_name()
		 *
		 * @return
		 */
		public function get_field_param( $param = '' ) {

			$field_param = '';
			if ( isset( $this->field[ $param ] ) ) {

				$field_param = $this->field[ $param ];
			}

			return $field_param;
		}

		/**
		 * LP_Fields::get_id()
		 *
		 * @param string $suffix
		 *
		 * @return
		 */
		private function get_id( $suffix = '' ) {
			return $suffix ? $this->get_name() . '_' . $suffix : $this->get_name();
		}

		/**
		 * LP_Fields::get_field()
		 *
		 * @return
		 */
		function get_value() {
			if ( ! $this->disabled ) {
				$data = new CHCH_LPF_Data( $this->lp, $this->template, $this->id );

				return $data->get_field_value( $this->option_group, $this->get_field_param( 'name' ) );
			} else {
				return 0;
			}
		}

		/**
		 * LP_Fields::get_field()
		 *
		 * @return
		 */
		function get_specific_value( $base, $option ) {
			$data = new CHCH_LPF_Data( $this->lp, $this->template, $this->id );

			return $data->get_field_value( $base, $option );
		}

		/**
		 * LP_Fields::get_field()
		 *
		 * @return
		 */
		function get_checkbox_vaule() {

			$checked      = '';
			$data         = new CHCH_LPF_Data( $this->lp, $this->template, $this->id );
			$checkbox_val = $data->get_field_value( $this->option_group, $this->get_field_param( 'name' ) );

			if ( $checkbox_val ) {
				$checked = 'checked';
			}

			return $checked;
		}

	}
}
