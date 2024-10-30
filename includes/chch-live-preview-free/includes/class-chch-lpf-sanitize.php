<?php
namespace ChopChop\LivePreviewFree;

if ( ! class_exists( 'ChopChop\\LivePreviewFree\\CHCH_LPF_Sanitize' ) ) {
	class CHCH_LPF_Sanitize {
		public static function sanitize_field( $field_type, $field_name, $new_values, $old_value ) {
			$sanitize_value = '';

			switch ( $field_type ) {
				case 'default':
					if ( isset( $new_values[ $field_name ] ) ) {
						$sanitize_value = self::sanitize_text( $new_values[ $field_name ] );
					} else {
						$sanitize_value = self::sanitize_text( $old_value );
					}
				break;

				case 'checkbox':
				case 'remover_checkbox':
					if ( isset( $new_values[ $field_name ] ) ) {
						$sanitize_value = 'on';
					} else {
						$sanitize_value = '';
					}
				break;

				case 'editor':
					if ( isset( $new_values[ $field_name ] ) ) {
						$sanitize_value = self::sanitize_editor( $new_values[ $field_name ] );
					} else {
						$sanitize_value = self::sanitize_editor( $old_value );
					}
				break;

				default:
					if ( isset( $new_values[ $field_name ] ) ) {
						$sanitize_value = self::sanitize_text( $new_values[ $field_name ] );
					} else {
						$sanitize_value = self::sanitize_text( $old_value );
					}
				break;
			}

			return $sanitize_value;
		}

		public static function sanitize_text( $new_value ) {
			return sanitize_text_field( $new_value );
		}

		public static function sanitize_editor( $new_value ) {
			return wp_kses_post( $new_value );
		}
	}
}