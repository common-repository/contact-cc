<?php

namespace ChopChop\ContactFormFree;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Email {
	/**
	 * Email constructor.
	 *
	 * @param $post_data
	 * @param $form_settings
	 */
	public function __construct( $post_data, $form_settings ) {
		$this->post_data     = $post_data;
		$this->form_settings = $form_settings;
	}

	function send() {

		$to_email = $this->form_settings[ 'email_address' ];

		$message = $this->build_email_content( $this->form_settings[ 'message_body' ] );
		$sent = wp_mail( $to_email, esc_html( $this->form_settings[ 'subject' ] ), $message );

		return $sent;
	}

	private function build_email_content( $message ) {
		if ( is_array( $this->post_data ) ) {
			foreach ( $this->post_data[ 'fields' ] as $field ) {
				if ( is_array( $field[ 'fieldVal' ] ) ) {
					$fieldVal = '';
					foreach ( $field[ 'fieldVal' ] as $val ) {

						$fieldVal .= $val . ", ";
					} 
					$fieldVal = rtrim($fieldVal, ", ");
				} else {
					$fieldVal = $field[ 'fieldVal' ];
				}
				$message = str_replace( "{" . $field[ 'fieldName' ] . "}", esc_html( $fieldVal ), $message );
			}
		}
		$message = preg_replace( '/\{[A-Za-z0-9]+\}/i', '', $message );

		return $message;
	}
}