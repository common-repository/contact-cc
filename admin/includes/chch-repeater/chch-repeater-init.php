<?php
namespace ChopChop\Repeater;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( !class_exists( 'CHCH_REPEATER_INIT_100' ) ) {

	class CHCH_REPEATER_INIT_100 {

		/**
		 * CHCH_REPEATER_INIT version.
		 *
		 * @var   string
		 * @since 1.0.0
		 */
		const VERSION = '1.0.0';

		const PRIORITY = 999;

		/**
		 * Instance of CHCH_REPEATER_INIT class
		 *
		 * @var object - CHCH_REPEATER_INIT
		 */
		public static $instance = null;

		/**
		 *  Setup class
		 *
		 * @since 1.0.0
		 */
		private function __construct() {
			add_action( 'init', array( $this, 'init_chch_repeater' ), self::PRIORITY );
		}

		public function init_chch_repeater() {

			if ( class_exists( 'ChChRepeater', false ) ) {
				return;
			}

			if ( !defined( 'CHCH_REPEATER_VERSION' ) ) {
				define( 'CHCH_REPEATER_VERSION', self::VERSION );
			}

			if ( !defined( 'CHCH_REPEATER_DIR' ) ) {
				define( 'CHCH_REPEATER_DIR', plugin_dir_path( __FILE__ ) );
			}

			if ( !defined( 'CHCH_REPEATER_URL' ) ) {
				define( 'CHCH_REPEATER_URL', plugin_dir_url( __FILE__ ) );
			}

			include_once('includes/chch-repeater-load.php');


		}

		/**
		 * Return an instance of CHCH_REPEATER_INIT class.
		 *
		 * @since  1.0.0
		 * @return CHCH_REPEATER_INIT single instance object
		 */
		public static function get_instance() {
			// If the single instance hasn't been set, set it now.
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Autoloads ChChRepeater classes files.
		 * @since  1.0.0
		 *
		 * @param  string $class_name - class name to load
		 */
		public function chch_repeater_autoload( $class_name ) {
			if ( 0 !== strpos( $class_name, 'ChChRepeater' ) ) {
				return;
			}

			if ( file_exists( CHCH_REPEATER_DIR . "includes/{$class_name}.php" ) ) {
				include_once( CHCH_REPEATER_DIR . "includes/{$class_name}.php" );
			}
		}

	}

	// Make it alive
	CHCH_REPEATER_INIT_100::get_instance();
}
