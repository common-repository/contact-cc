<?php
namespace ChopChop\LivePreviewFree;

if ( ! class_exists( 'ChopChop\\LivePreviewFree\\CHCH_LPF_Views' ) ) {
	/**
	 *
	 *
	 * @package   CHC LIVE PREVIEW
	 * @author
	 * @copyright 2015
	 * @version   1.0.0
	 * @access    public
	 */
	class CHCH_LPF_Templates {

		private $tpl_id = '';

		public function __construct( CHCH_LPF $lp, $tpl_id, $lp_post_id ) {
			$this->lp         = $lp;
			$this->tpl_id     = $tpl_id;
			$this->lp_post_id = $lp_post_id;
		}

		public function get_template() {
			$tpl_dir          = $this->lp->tpl_dir;
			$template_options = $this->get_template_options( $this->lp_post_id );
			if ( file_exists( $tpl_dir . $this->tpl_id . '/template.php' ) ) {

				$id       = $this->lp_post_id;
				$template = $this;
				include( $tpl_dir . $this->tpl_id . '/template.php' );
			}
		}

		function get_template_options( $id ) {
			$data = new CHCH_LPF_Data( $this->lp, $this->tpl_id, $this->lp_post_id );

			return $data->get_fields_values( $id );
		}

		public function get_template_option( $base, $option ) {
			$data = new CHCH_LPF_Data( $this->lp, $this->tpl_id, $this->lp_post_id );

			return $data->get_field_value( $base, $option );
		}

		public function build_css( $prefix = 's' ) {
			$lp_fields = $this->lp->get_all_fields_group();

			echo '<style>';

			do_action( 'chch_lpf_css_before', $this->lp_post_id );

			foreach ( $lp_fields as $group => $fields ) {
				echo $this->build_css_from_fields( $fields, $group, $prefix );
			}

			do_action( 'chch_lpf_css_after', $this->lp_post_id );

			echo '</style>';
		}

		public function build_css_from_fields( $fields, $group, $prefix ) {

			$fields_css = '';
			foreach ( $fields as $field ) {
				$exclude_types = array(
					'attr',
					'class_switcher',
				);
				if ( isset( $field[ 'type' ] ) ) {
					if ( in_array( $field[ 'type' ], $exclude_types ) ) {
						continue;
					}
				}
				$lp_field     = new CHCH_LPF_Fields( $field, $group, $this->tpl_id, $this->lp, $this->lp_post_id );
				$field_val    = $lp_field->get_value( $this->lp_post_id );
				$field_target = $lp_field->get_field_param( 'target' );
				$field_attr   = $lp_field->get_field_param( 'attr' );
				$value_unit   = $lp_field->get_field_param( 'unit' );
				$field_type   = $lp_field->get_field_param( 'type' );

				if ( $field_val == '' ) {
					continue;
				}

				if ( $field_type == 'font' ) {
					$field_val = str_replace( '+', ' ', $field_val );
				}

				if ( $field_type !== 'revealer_group' ) {
					$fields_css .= $this->build_css_regule( $prefix, $field_target, $field_attr, $field_val, $value_unit );
				} else {
					if ( $revealer_sections = $lp_field->get_field_param( 'revaeals' ) ) {
						foreach ( $revealer_sections as $revealer_fields ) {
							if ( isset( $revealer_fields[ 'section_id' ] ) && $revealer_fields[ 'section_id' ] == $field_val ) {
								foreach ( $revealer_fields[ 'fields' ] as $revealer_field ) {
									$lp_revealer_field = new CHCH_LPF_Fields( $revealer_field, $group, $this->tpl_id, $this->lp, $this->lp_post_id );
									$field_val         = $lp_revealer_field->get_value( $this->lp_post_id );
									$field_target      = $lp_revealer_field->get_field_param( 'target' );
									$field_attr        = $lp_revealer_field->get_field_param( 'attr' );
									$value_unit        = $lp_revealer_field->get_field_param( 'unit' );

									$fields_css .= $this->build_css_regule( $prefix, $field_target, $field_attr, $field_val, $value_unit );

									if ( $additional_css = $lp_revealer_field->get_field_param( 'add_css' ) && is_array( $lp_revealer_field->get_field_param( 'add_css' ) ) ) {

										foreach ( $lp_revealer_field->get_field_param( 'add_css' ) as $css_rule ) {
											$fields_css .= $this->build_css_regule( $prefix, $field_target, $css_rule[ 'attr' ], $css_rule[ 'value' ], $value_unit );
										}
									}
								}
							}
						}
					}
				}
			}

			return $fields_css;
		}

		/**
		 * LP_Templates::build_css_regule()
		 *
		 */
		private function build_css_regule( $prefix, $field_target, $field_attr, $field_val, $value_unit ) {
			$css_rule = '';

			if ( $prefix && $field_attr ) {

				$css_rule .= sprintf( "%s %s{\n", $prefix, $field_target );

				if ( $field_attr != 'background-image' ) {
					$css_rule .= sprintf( "\t\t%s:%s%s !important;\n", $field_attr, $field_val, $value_unit );
				} else {
					$css_rule .= sprintf( "\t\t%s:url(\"%s\") !important;\n", $field_attr, $field_val );
				}

				$css_rule .= "\t}\n";
			}

			return $css_rule;
		}

		/**
		 * LP_Templates::include_fonts()
		 *
		 * @return
		 */
		public function include_fonts() {
			$lp_fields = $this->lp->get_all_fields_group();

			foreach ( $lp_fields as $group => $fields ) {

				foreach ( $fields as $field ) {
					$this->enqueue_fonts( $field, $group );
				}
			}
		}

		/**
		 * LP_Templates::enqueue_fonts()
		 *
		 * @param mixed $field
		 *
		 * @return
		 */
		private function enqueue_fonts( $field, $group ) {
			if ( isset( $field[ 'type' ] ) && $field[ 'type' ] == 'font' ) {
				$lp_field  = new LP_Fields( $field, $group, $this->tpl_id, $this->lp, $this->lp_post_id );
				$field_val = $lp_field->get_value( $this->lp_post_id );

				wp_enqueue_style( 'chch_lp_' . str_replace( '+', '-', $field_val ), '//fonts.googleapis.com/css?family=' . $field_val, array(), CHCH_LIVE_PREVIEW_FREE_VERSION::VERSION );
			}
		}

		/**
		 * Returns or prints template thumbnail
		 *
		 * @param string $template_id - thumbnail will be included from template with this id
		 * @param string $echo        - Default - false. If set to true thumbnail will be printed immediately, otherwise it will be return as a string
		 *
		 * @return string $thumbnail - only if $echo is set to false
		 */
		public static function get_thumbnail( $template_id = '', $tpls_dir = '', $tpls_url = '', $echo = false ) {

			if ( empty( $template_id ) ) {
				$template_id = self::tpl_id;
			}

			if ( empty( $tpls_dir ) ) {
				$tpls_dir = self::$tpls_dir;
			}

			if ( empty( $tpls_url ) ) {
				$tpls_url = self::$tpls_url;
			}

			$thumbnail = '';

			if ( file_exists( $tpls_dir . $template_id . '/screenshot.png' ) ) {

				$thumbnail = sprintf( '<img src="%1$s%2$s/screenshot.png" alt="%2$s-thumbnail" />', $tpls_url, $template_id );
			}

			if ( $echo ) {
				echo $thumbnail;
			} else {
				return $thumbnail;
			}
		}

	}
}
