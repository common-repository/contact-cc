<?php
namespace ChopChop\ContactFormFree;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'FormGenerator' ) ) {

	class FormGenerator {

		/**
		 * Generate form view
		 *
		 * @param $fields
		 * @param $messages
		 * @param $form_id
		 */
		public static function get_form( $fields, $messages, $form_id, $button ='' ) {
			if ( ! is_admin() ) {
				echo "<form action=\"#\" class=\"chch-contact-free__form\">\n";
			} else {
				echo "<div class=\"chch-contact-free__form\">\n";
			}

			echo '<div class="chch-contact-free__input-groups-wrapper">';
			foreach ( $fields as $field ) {
				if ($field['id']) {
					print( "<div class=\"chch-contact-free__input-group\"> \n" );
					$field = new FormFields( $field );
					$field->get_field();
					echo '<div class="chch-contact-free__error"></div>';
					echo "</div>";
				}
			}
			echo '</div>';

			printf( "\t<div class=\"chch-contact-free__success\">%s</div> \n", $messages[ 'success' ] );
			printf( "\t<div class=\"chch-contact-free__error-main\">%s</div> \n", $messages[ 'failed' ] );
			echo '<div style="display:none">';
			printf( "\t<input type=\"hidden\" name=\"_chch_cff_nonce\" id=\"_chch_cff_nonce\" value=\"%s\">", wp_create_nonce( "chch-cff-subscribe" ) );
			printf( "\t<input type=\"hidden\" name=\"_chch_contact_form_id\" id=\"_chch_contact_form_id\" value=\"%s\">", $form_id );
			echo '</div>';
			$type = is_admin() ? 'button' : 'submit';
			printf( "\t<button type=\"%s\" class=\"chch-contact-free__btn\" data-id='%s'><i class=\"fa fa-spinner fa-spin fa-2x\"></i>%s</button>", $type, $form_id, $button );

			if ( ! is_admin() ) {
				echo "</form>";
			} else {
				echo "</div>";
			}
		}

	}
}