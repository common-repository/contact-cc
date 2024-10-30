<?php
namespace ChopChop\Repeater;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( !class_exists( 'ChChRepeaterView' ) ) {

	if ( file_exists( "../chch-repeater.php" ) ) {
		include_once( "../chch-repeater.php" );
	}

	if ( file_exists( plugin_dir_path( __FILE__ ) . "includes/chch-repeater-fields.php" ) ) {
		include_once( plugin_dir_path( __FILE__ ) . "includes/chch-repeater-fields.php" );
	}

	class ChChRepeaterView {

		const VERSION = '1.0.0';
		private $repeater_model;

		/**
		 * Register all fields for repeater
		 *
		 * @param ChChRepeater $repeater_model
		 *
		 */
		function __construct( ChChRepeater $repeater_model ) {
		}

		public static function get_repeater( $id, $fields, $saved_data ) {
			echo "<div class=\"chch-repeater\">\n";
			echo "<div class=\"chch-repeater-row-wrapper\">\n";
			$i = 0;
			$registered_fields = $fields;
			if ( !empty( $saved_data ) ) {

				foreach ( $saved_data as $fields ) {
					self::get_row( $i, $registered_fields, $id, $fields );
					$i++;
				}
			} else {
				self::get_row( $i, $registered_fields, $id );
			}

			echo "</div>\n";
			echo '<a class="chch-repeater-add-row button button-primary" href="#" data-row-count="' . $i . '" data-id="_' . $id . '">' . __( 'Add Field', 'ChChRepeater' ) . '</a>';
			echo "</div>\n";
		}

		/**
		 * @param int    $row_number
		 * @param array  $fields
		 * @param        $id
		 * @param string $saved_data
		 * @param array  $parent_data
		 *
		 */
		public static function get_row( $row_number = 0, array $fields, $id, $saved_data = '', $parent_data = array() ) {
			if ( !empty( $parent_data ) ) {
				$parent_data = wp_parse_args( $parent_data, array(
					'id'      => 'repeater',
					'row_num' => 0,
				) );
			}

			echo "<div class=\"chch-repeater-row\">\n";
			printf( "\t<div class=\"field-count\">%s</div>\n", $row_number + 1 );
			echo "<div class=\"chch-repeater-fields\">\n";

			foreach ( $fields as $field ) {

				$field_obj = new ChChRepeaterField( $field, $row_number, $id, $saved_data, $parent_data );
				$field_obj->get_field();
			}

			echo "</div>\n";
			$hide_section = $row_number == 0 ? 'hide-section' : '';
			echo "<div class=\"chch-repeater-delete-row\"><a href=\"#\" class=\"delete-email-field ".$hide_section."\"><span class=\"dashicons dashicons-dismiss\"></span></a></div>";

			echo "</div>\n";
		}
	}
}