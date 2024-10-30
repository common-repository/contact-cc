<?php
namespace ChopChop\ContactFormFree;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'CHCH_CONTACT_FORM_INIT' ) ) {

	class CHCH_CONTACT_FORM_INIT {

		/**
		 * CHCH_REPEATER_INIT version.
		 *
		 * @var   string
		 * @since 1.0.0
		 */
		const VERSION = '1.0.0';

		const PRIORITY = 999;

		/**
		 * Instance of CHCH_CONTACT_FORM_INIT_100 class
		 *
		 * @var object - CHCH_CONTACT_FORM_INIT_100
		 */
		public static $instance = null;

		private function __construct() {
			if ( class_exists( 'ContactForm', false ) ) {
				return;
			}

			add_action( 'init', array( $this, 'init_chch_contact_form_free' ), self::PRIORITY );
		}

		public function init_chch_contact_form_free() {
			if ( ! defined( 'CHCH_CONTACT_FORM_VERSION' ) ) {
				define( 'CHCH_CONTACT_FORM_VERSION', self::VERSION );
			}

			if ( ! defined( 'CHCH_CONTACT_FORM_DIR' ) ) {
				define( 'CHCH_CONTACT_FORM_DIR', plugin_dir_path( __FILE__ ) );
			}

			if ( ! defined( 'CHCH_CONTACT_FORM_URL' ) ) {
				define( 'CHCH_CONTACT_FORM_URL', plugin_dir_url( __FILE__ ) );
			}
			if ( file_exists( CHCH_CONTACT_FORM_DIR . 'includes/ContactForm.php' ) ) {
				include_once( CHCH_CONTACT_FORM_DIR . 'includes/ContactForm.php' );
			}

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
			add_action( 'wp_ajax_chch_lpf_subscribe', array( $this, 'subscribe' ) );
			add_action( 'wp_ajax_nopriv_chch_lpf_subscribe', array( $this, 'subscribe' ) );
		}

		/**
		 * Return an instance of CHCH_CONTACT_FORM_INIT_100 class.
		 *
		 * @since  1.0.0
		 * @return CHCH_CONTACT_FORM_INIT_100 single instance object
		 */
		public static function get_instance() {
			// If the single instance hasn't been set, set it now.
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Cloning is forbidden.
		 *
		 * @since 1.0.0
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'Nope!', 'chch-contact' ), '1.0.0' );
		}

		/**
		 * Don't wake me up!
		 *
		 * @since 1.0.0
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, __( 'Nope!', 'chch-contact' ), '1.0.0' );
		}

		public function enqueue() {
			wp_register_script( 'chch-contact-form', CHCH_CONTACT_FORM_URL . 'assets/js/public.js', array( 'jquery' ), CHCH_CONTACT_FORM_VERSION );
			wp_enqueue_script( 'chch-contact-form' );
			wp_localize_script( 'chch-contact-form', 'chch_contact_form_ajax_object', array( 'ajaxUrl' => admin_url( 'admin-ajax.php' ) ) );
		}

		/**
		 *
		 */
		public function subscribe() {
			if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'chch-cff-subscribe' ) ) {
				echo json_encode( array( 'status' => 'error', 'message' => 'Cheating ?' ) );
				die();
			}

			if ( isset( $_POST['form_id'] ) ) {
				$form_id = intval( $_POST['form_id'] );
				$form    = new ContactForm( $form_id );
				$form->subscribe();
				die();
			} else {
				echo json_encode( array( 'status' => 'error', 'message' => 'Something went wrong' ) );
				die();
			}
			die();
		}

	}

	// Make it alive
	CHCH_CONTACT_FORM_INIT::get_instance();
}
