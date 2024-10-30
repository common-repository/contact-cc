<?php
/**
 * DESC
 *
 * @package   ChopChop\LivePreviewFree
 * @author    Chop-Chop.org <shop@chop-chop.org>
 * @license   GPL-2.0+
 * @link      https://shop.chop-chop.org
 * @copyright 2016
 *
 * @wordpress-plugin
 */

namespace ChopChop\LivePreviewFree;
 
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'CHCH_LPF_Init_100' ) ) {

	/**
	 * Class CHCH_LPF_Init_100
	 *
	 * @package ChopChop\LivePreviewFree
	 */
	class CHCH_LPF_Init_100 {

		/**
		 * CHCH_LPF_Init_100 version.
		 *
		 * @param   string
		 * @since 1.0.0
		 */
		const VERSION = '1.0.0';

		/**
		 * Load priority.
		 *
		 * @param   string
		 * @since 1.0.0
		 */
		const PRIORITY = 9999;

		/**
		 * Instance of CHCH_LPF_Init_100 class
		 *
		 * @var object - CHCH_LPF_Init_100
		 * @since 1.0.0
		 */
		public static $instance = null;

		/**
		 * CHCH_LPF_Init_100 constructor.
		 *
		 * @since 1.0.0
		 */
		private function __construct() {
			add_action( 'init', array( $this, 'init_chch_live_preview_free' ), self::PRIORITY );
		}

		/**
		 *  Load all LP files and actions.
		 *  If main LP class already exists loading is canceled.
		 */
		public function init_chch_live_preview_free() {

			if ( class_exists( 'ChopChop\\LivePreviewFree\\CHCH_LPF', false ) ) {
				return;
			}

			if ( ! defined( 'CHCH_LIVE_PREVIEW_FREE_VERSION' ) ) {
				define( 'CHCH_LIVE_PREVIEW_FREE_VERSION', self::VERSION );
			}

			if ( ! defined( 'CHCH_LIVE_PREVIEW_FREE_DIR' ) ) {
				define( 'CHCH_LIVE_PREVIEW_FREE_DIR', plugin_dir_path( __FILE__ ) );
			}

			if ( ! defined( 'CHCH_LIVE_PREVIEW_FREE_URL' ) ) {
				define( 'CHCH_LIVE_PREVIEW_FREE_URL', plugin_dir_url( __FILE__ ) );
			}

			$this->load_files();

			do_action( 'chch_lpf_init' );

			CHCH_LPF_WP_Hooks::get_instance();
		}

		/**
		 * Return an instance of CHCH_LPF class.
		 *
		 * @since  1.0.0
		 * @return CHCH_LPF_Init_100 single instance object
		 */
		public static function get_instance() {
			// If the single instance hasn't been set, set it now.
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Load necessary files.
		 *
		 * @since  1.0.0
		 */
		public static function load_files() {
			if ( file_exists( dirname( __FILE__ ) . '/includes/class-chch-lpf-objects.php' ) ) {
				require_once( dirname( __FILE__ ) . '/includes/class-chch-lpf-objects.php' );
			}

			if ( file_exists( dirname( __FILE__ ) . '/includes/class-chch-lpf.php' ) ) {
				require_once( dirname( __FILE__ ) . '/includes/class-chch-lpf.php' );
			}

			if ( file_exists( dirname( __FILE__ ) . '/includes/class-chch-lpf-sanitize.php' ) ) {
				require_once( dirname( __FILE__ ) . '/includes/class-chch-lpf-sanitize.php' );
			}

			if ( file_exists( dirname( __FILE__ ) . '/includes/class-chch-lpf-data.php' ) ) {
				require_once( dirname( __FILE__ ) . '/includes/class-chch-lpf-data.php' );
			}

			if ( file_exists( dirname( __FILE__ ) . '/includes/class-chch-lpf-templates.php' ) ) {
				require_once( dirname( __FILE__ ) . '/includes/class-chch-lpf-templates.php' );
			}

			if ( file_exists( dirname( __FILE__ ) . '/includes/class-chch-lpf-fields.php' ) ) {
				require_once( dirname( __FILE__ ) . '/includes/class-chch-lpf-fields.php' );
			}

			if ( file_exists( dirname( __FILE__ ) . '/includes/class-chch-lpf-form.php' ) ) {
				require_once( dirname( __FILE__ ) . '/includes/class-chch-lpf-form.php' );
			}

			if ( file_exists( dirname( __FILE__ ) . '/includes/class-chch-lpf-views.php' ) ) {
				require_once( dirname( __FILE__ ) . '/includes/class-chch-lpf-views.php' );
			}

			if ( file_exists( dirname( __FILE__ ) . '/includes/class-chch-lpf-wp-hooks.php' ) ) {
				require_once( dirname( __FILE__ ) . '/includes/class-chch-lpf-wp-hooks.php' );
			}
		}
	}

	// Make it alive.
	CHCH_LPF_Init_100::get_instance();
}
