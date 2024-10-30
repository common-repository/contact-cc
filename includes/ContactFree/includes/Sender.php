<?php

namespace ChopChop\ContactFormFree;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Sender {

	/**
	 * Data send from form by $_POST
	 *
	 * @type array
	 */
	private $form_type;

	/**
	 * Send settings. Form type, api key etc.
	 *
	 * @type array
	 */
	private $form_type_settings;

	/**
	 * Form settings. Includes fields and messages.
	 *
	 * @type array
	 */
	private $fields;

	/**
	 * Form settings. Includes fields and messages.
	 *
	 * @type array
	 */
	private $post_data;

	/**
	 * Sender constructor.
	 *
	 * @param       $form_type
	 * @param       $form_type_settings
	 * @param       $fields
	 * @param       $post_data
	 * @param       $messages
	 */
	function __construct( $form_type, $form_type_settings, $fields, $post_data, $messages ) {
		$this->form_type          = $form_type;
		$this->form_type_settings = $form_type_settings;
		$this->fields             = $fields;
		$this->post_data          = $post_data;
		$this->messages           = $messages;
	}

	/**
	 * Wrapper for send action.
	 *
	 * @uses $this->validate_fields
	 */
	public function send() {
		$validate = $this->validate_fields();
		if ( ! empty( $validate ) ) {
			$response           = array();
			$response['errors'] = $validate;
			$response['status'] = 'fields_error';
			print json_encode( $response );
			die();
		}

		if ( class_exists( 'ChopChop\\ContactFormFree\\Email' ) ) {

			$subscribe = new Email( $this->post_data, $this->form_type_settings );
			$send      = $subscribe->send();

			if ( $send ) {
				$response['status'] = 'ok';
				print json_encode( $response );
				die();
			} else {
				$response['status'] = 'error';
				print json_encode( $response );
				die();
			}
		}
	}

	private function validate_fields() {
		$errors = array();

		if ( is_array( $this->fields ) ) {

			foreach ( $this->fields as $field ) {
				$field_id = $field['id'];

				if ( isset( $this->post_data['fields'][ $field_id ]['fieldVal'] ) && $this->post_data['fields'][ $field_id ]['fieldVal'] !== '' ) {
					$posted_field = $this->post_data['fields'][ $field_id ];

					if ( ! $this->validate_field_format( $field['type'], $this->post_data['fields'][ $field_id ]['fieldVal'] ) ) {
						$errors[] = array(
							'error_type'    => 'format_error',
							'field_name'    => $field_id,
							'error_message' => $this->messages['format_error'],
						);
					}
				} elseif ( isset( $field['req'] ) && $field['req'] == 'yes' ) {
					$errors[] = array(
						'error_type'    => 'field_req',
						'field_name'    => $field_id,
						'error_message' => $this->messages['req_error'],
					);
				}
			}
		}

		return $errors;
	}

	private function validate_field_format( $field_type, $posted_value ) {
		switch ( $field_type ) {
			case 'email':
				return filter_var( $posted_value, FILTER_VALIDATE_EMAIL );
			break;

			case 'number':
				return is_numeric( $posted_value );
			break;

			case 'url':
				return filter_var( $posted_value, FILTER_VALIDATE_URL );
			break;

			default:
				return true;
			break;
		}
	}
}