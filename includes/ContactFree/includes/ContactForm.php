<?php
namespace ChopChop\ContactFormFree;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'ChopChop\\ContactFormFree\\ContactForm' ) ) {

	function chch_contact_form_free_autoload( $class_name ) {
		ContactForm::autoload( $class_name );
	}

	spl_autoload_register( '\ChopChop\ContactFormFree\chch_contact_form_free_autoload' );

	class ContactForm {

		private $fields = array();
		private $default_fields = array(
			array(
				'type'        => 'email',
				'placeholder' => 'E-mail',
				'id'          => 'email',
				'label'       => 'E-mail',
				'req'         => true,
			),
		);

		private $messages = array();
		private $default_messages = array(
			'success'      => 'Thank You! So much',
			'failed'       => 'Something went very wrong!',
			'format_error' => 'This value is invalid.',
			'req_error'    => 'This field is mandatory.',
		);

		private $settings = array();
		private $default_settings = array(
			'type'          => 'email',
			'type_settings' => array(
				'email_address' => '',
				'message_body'  => '',
				'headers'       => '',
			),
		);

		private $form_id;

		function __construct( $form_id ) {
			if ( $this->check_form( $form_id ) ) {
				$this->form_id = $form_id;
			} else {
				return;
			}

			$this->form_type          = $this->get_form_type();
			$this->form_type_settings = $this->get_form_type_settings();
			$this->fields             = $this->get_form_fields();
			$this->messages           = $this->get_form_messages();
		}

		/**
		 * Check post with given id.
		 *
		 * @since    1.0.0
		 *
		 * @param $form_id
		 *
		 * @return bool
		 */
		private function check_form( $form_id ) {
			$post_type = get_post_type( $form_id );

			if ( $post_type && $post_type == 'chch-contact-free' ) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Get contact form type.
		 *
		 * @since    1.0.0
		 *
		 * @return string
		 *
		 */
		private function get_form_type() {
			$default_type = 'email';
			$type         = get_post_meta( $this->form_id, '_chch_contact_free_contact_type', true );
			$type         = $type ? $type : $default_type;

			return $type;
		}

		/**
		 * Get contact form fields.
		 *
		 * @since    1.0.0
		 *
		 *
		 */
		private function get_form_type_settings() {
			return get_post_meta( $this->form_id, $this->form_type . '_data', true );
		}

		/**
		 * Get contact form fields.
		 *
		 * @since    1.0.0
		 *
		 *
		 */
		private function get_form_fields() {

			return isset( $this->form_type_settings[ 'fields' ] ) && ! empty( $this->form_type_settings[ 'fields' ] ) ? $this->form_type_settings[ 'fields' ] : $this->default_fields;
		}

		/**
		 * Get contact form fields.
		 *
		 * @since    1.0.0
		 *
		 *
		 */
		private function get_form_messages() {
			$messages = array();
			if ( $thank_you = get_post_meta( $this->form_id, '_chch_contact_free_message_success', true ) ) {
				$messages[ 'success' ] = $thank_you;
			}

			if ( $failed = get_post_meta( $this->form_id, '_chch_contact_free_message_failed', true ) ) {
				$messages[ 'failed' ] = $failed;
			}

			if ( $format_error = get_post_meta( $this->form_id, '_chch_contact_free_message_format_error', true ) ) {
				$messages[ 'format_error' ] = $format_error;
			}

			if ( $req_error = get_post_meta( $this->form_id, '_chch_contact_free_message_req_error', true ) ) {
				$messages[ 'req_error' ] = $req_error;
			}

			return $this->parse_messages( $messages );
		}

		private function parse_messages( $messages ) {

			if ( is_array( $messages ) ) {
				$parsed_array = wp_parse_args( $messages, $this->default_messages );
			} else {
				$parsed_array = $this->messages;
			}

			return $parsed_array;
		}

		/**
		 * Autoload Contact Form PRO classes.
		 * Callback for spl_autoload_register
		 *
		 * @param $class
		 *
		 * @return bool
		 */
		static public function autoload( $class ) {
			$class = str_replace( 'ChopChop\\ContactFormFree\\', '', $class );
			if ( file_exists( plugin_dir_path( __FILE__ ) . $class . ".php" ) ) {
				include_once( plugin_dir_path( __FILE__ ) . $class . ".php" );
			} else {
				return false;
			}
		}

		/**
		 * Get form view.
		 *
		 * @uses FormGenerator
		 */
		public function get_form( $button = '' ) {
			FormGenerator::get_form( $this->fields, $this->messages, $this->form_id, $button );
		}

		/**
		 * Subscribe function
		 *
		 * @uses Sender
		 */
		public function subscribe() {
			$posted_data = $_POST;
			$sender      = new Sender( $this->form_type, $this->form_type_settings, $this->fields, $posted_data, $this->messages );
			$sender->send();
		}

	}
}
