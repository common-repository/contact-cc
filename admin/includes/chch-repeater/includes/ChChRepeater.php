<?php
namespace ChopChop\Repeater;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( !class_exists( 'ChChRepeater' ) ) {

	class ChChRepeater {

		/**
		 * Class version
		 */
		const VERSION = '1.0.0';

		/**
		 * @type array
		 */
		private $fields;
		/**
		 * @type bool
		 */
		private $saved_data;

		/**
		 * Setup class
		 *
		 * @param string     $id
		 * @param array      $fields
		 * @param array|bool $saved_data
		 */
		function __construct($id = '', array $fields, $saved_data = false  ) {
			if ( empty( $id ) ) {
				return;
			}

			$this->id = $id;
			$this->fields = $fields;
			$this->saved_data = $saved_data;
		}

		/**
		 * Magic getter for our object.
		 *
		 * @since  0.1.0
		 *
		 * @param string $field
		 *
		 * @throws \Exception Throws an exception if the field is invalid.
		 * @return mixed
		 */
		public function __get( $field ) {
			switch ( $field ) {
				case 'version':
					return self::VERSION;
				case 'id':
				case 'fields':
				case 'saved_data':
					return $this->$field;
				default:
					throw new \Exception( 'Invalid ' . __CLASS__ . ' property: ' . $field );
			}
		}

		/**
		 *  Get repeater view.
		 *
		 * @uses ChChRepeaterView
		 */
		public function get_repeater() {
			ChChRepeaterView::get_repeater( $this->id, $this->fields, $this->saved_data);
		}

		/**
		 * Register and enqueue scripts.
		 * NOTICE: it's static function which You have to call manually (ChChRepeater::enqueue_scripts) in Your wp_enqueue_script hook function
		 */
		public static function enqueue_scripts() {
			wp_register_style( 'chch-repeater-styles', CHCH_REPEATER_URL . 'assets/css/style.css', array(), ChChRepeater::VERSION );
			wp_register_script( 'chch-repeater-script', CHCH_REPEATER_URL . 'assets/js/chch-revealer.js', array(), ChChRepeater::VERSION, true );

			wp_enqueue_style( 'chch-repeater-styles' );
			wp_enqueue_script( 'chch-repeater-script' );
		}
	}
}
