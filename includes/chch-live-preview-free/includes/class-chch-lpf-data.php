<?php
namespace ChopChop\LivePreviewFree;

if ( ! class_exists( 'ChopChop\\LivePreviewFree\\CHCH_LPF_Data' ) ) {
	/**
	 * CHCH_LPF_Data
	 *
	 * @package   CHCH LIVE PREVIEW
	 * @author
	 * @copyright 2015
	 * @access    public
	 */
	class CHCH_LPF_Data {

		/**
		 * CHCH_LPF_Data::__construct()
		 *
		 * @return
		 */
		public function __construct( CHCH_LPF $lp, $template, $id ) {
			$this->lp       = $lp;
			$this->template = $template;
			$this->id       = $id;
		}

		/**
		 * CHCH_LPF_Data::get_fields_values()
		 *
		 * @return
		 */
		public function get_fields_values() {
			$tpl_dir = $this->lp->tpl_dir;
			if ( ! $options = get_post_meta( $this->id, '_' . $this->template . '_template_data', true ) ) {
				return $this->get_fields_defaults();
			}

			return $options;
		}

		/**
		 * CHCH_LPF_Data::get_field_value()
		 *
		 * @return
		 */
		public function get_field_value( $base, $option ) {

			$all_options = $this->get_fields_values();

			if ( isset( $all_options[ $base ][ $option ] ) ) {

				return $all_options[ $base ][ $option ];
			} else {

				$default_options = $this->get_fields_defaults();

				if ( isset( $default_options[ $base ][ $option ] ) ) {
					return $default_options[ $base ][ $option ];
				}
			}

			return '';
		}

		/**
		 * LP_Data::get_fields_defaults()
		 *
		 * @return
		 */
		public function get_fields_defaults() {

			$tpl_dir = $this->lp->tpl_dir;

			if ( file_exists( $tpl_dir . $this->template . '/defaults.php' ) ) {
				return $options = ( include( $tpl_dir . $this->template . '/defaults.php' ) );
			}

			return '';
		}

		/**
		 * LP_Data::save_post_fields()
		 *
		 * @param mixed $post_data
		 * @param mixed $lp_post_id
		 *
		 * @return void
		 */
		public function save_meta( $meta_name, $meta_value, $id ) {
			$meta_value = sanitize_text_field( $meta_value );
			update_post_meta( $id, $meta_name, $meta_value );
		}

		/**
		 * LP_Data::save_post_fields()
		 *
		 * @param mixed $post_data
		 * @param mixed $lp_post_id
		 *
		 * @return void
		 */
		public function save_post_fields( $post_data, $lp_post_id ) {

			//get all fields sections
			$fields_to_save = $this->lp->get_all_fields_group();

			$sanitize_data = array();

			if ( ! isset( $post_data[ $this->template . '-form-loaded' ] ) ) {
				$post_data = $this->get_fields_defaults();
			}

			foreach ( $fields_to_save as $id => $fields ) {
				$sanitize_data[ $id ] = $this->sanitize_fields( $fields, $id, $post_data, $lp_post_id );
			}
			//return $sanitize_data;
			if ( $sanitize_data ) {
				$this->save_data( $sanitize_data, $lp_post_id );
			}
		}

		/**
		 * LP_Data::save_fields()
		 *
		 * @param mixed $fields
		 * @param mixed $group_id
		 * @param mixed $post_data
		 * @param mixed $lp_post_id
		 *
		 * @return void
		 */
		public function sanitize_fields( $fields, $group_id, $post_data, $lp_post_id ) {

			$group_fields = array();

			foreach ( $fields as $field ) {

				$lp_field        = new CHCH_LPF_Fields( $field, $group_id, $this->template, $this->lp, $this->id );
				$old_val         = $lp_field->get_value( $lp_post_id );
				$field_post_name = $lp_field->get_name();
				$field_type      = $lp_field->get_field_param( 'type' );
				$field_name      = $lp_field->get_field_param( 'name' );

				$group_fields[ $field_name ] = CHCH_LPF_Sanitize::sanitize_field( $field_type, $field_post_name, $post_data, $old_val );

				if ( $field_type === 'revealer_group' ) {
					if ( $revealer_sections = $lp_field->get_field_param( 'revaeals' ) ) {
						foreach ( $revealer_sections as $revealer_fields ) {
							foreach ( $revealer_fields[ 'fields' ] as $revealer_field ) {
								$lp_revealer_field = new CHCH_LPF_Fields( $revealer_field, $group_id, $this->template, $this->lp, $this->id );
								$old_val           = $lp_revealer_field->get_value( $lp_post_id );
								$field_post_name   = $lp_revealer_field->get_name();
								$field_type        = $lp_revealer_field->get_field_param( 'type' );
								$field_name        = $lp_revealer_field->get_field_param( 'name' );

								$group_fields[ $field_name ] = CHCH_LPF_Sanitize::sanitize_field( $field_type, $field_post_name, $post_data, $old_val );
							}
						}
					}
				}
			}

			return $group_fields;
		}

		/**
		 * LP_Data::save_post_fields()
		 *
		 * @param mixed $post_data
		 * @param mixed $lp_post_id
		 *
		 * @return void
		 */
		public function save_data( $sanitize_data, $lp_post_id ) {
			$this->save_meta( '_chch_lpf_template', $this->template, $lp_post_id );

			return update_post_meta( $lp_post_id, '_' . $this->template . '_template_data', $sanitize_data );
		}
	}
}