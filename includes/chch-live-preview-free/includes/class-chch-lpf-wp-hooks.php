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

if ( ! class_exists( 'ChopChop\\LivePreviewFree\\LP_WP_Hooks' ) ) {

	/**
	 * Class CHCH_LPF_WP_Hooks
	 *
	 * @package ChopChop\LivePreviewFree
	 */
	class CHCH_LPF_WP_Hooks {

		/**
		 * Instance of LP_WP_Hooks class
		 *
		 * @var object - LP_WP_Hooks.
		 */
		public static $instance = null;

		/**
		 * Instance of LP_wp_hooks class
		 *
		 * @var object - LP_wp_hooks
		 */
		private $lp_post_types = array( 'post' );

		/**
		 * LP_WP_Hooks constructor.
		 */
		private function __construct() {
			$this->lp_objects = is_array( CHCH_LPF_Objects::get_all() ) ? CHCH_LPF_Objects::get_all() : null;

			if ( null !== $this->lp_objects ) {
				$this->lp_post_types = CHCH_LPF_Objects::get_all_post_types();
			}

			if ( is_admin() ) {
				$this->admin_hooks();
			}
		}

		/**
		 * Return an instance of CHCH_LPF class.
		 *
		 * @since  1.0.0
		 * @return CHCH_LPF single instance object
		 */
		public static function get_instance() {
			// If the single instance hasn't been set, set it now.
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Return an instance of CHCH_LPF class.
		 *
		 * @since  1.0.0
		 * @return CHCH_LPF single instance object
		 */
		private function get_all_post_types() {

			$post_types = array();

			foreach ( $this->lp_objects as $lp ) {
				$post_types[ $lp->target_post_types ];
			}

			return $post_types;
		}

		/**
		 * Check if given post type is in post types array.
		 *
		 * @param string $check - post type slug.
		 *
		 * @return mixed
		 */
		private function check_post_type( $check ) {

			return array_search( $check, $this->lp_post_types );
		}

		/**
		 * All necessary Wordpress hooks.
		 */
		public function admin_hooks() {

			add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'edit_form_after_title', array( $this, 'templates_view' ) );

			add_action( 'save_post', array( $this, 'save_lp_data' ), 10, 3 );

			add_action( 'admin_init', array( $this, 'chch_lp_tinymce_event' ) );

			add_action( 'wp_ajax_chch_lpf_load_lp_form', array( $this, 'ajax_load_lpf_form' ) );
		}

		/**
		 * Callback for admin_enqueue_scripts hook.
		 * Register scripts and styles.
		 */
		public function register_scripts() {
			$this->register_styles();
			$this->register_js();
		}

		/**
		 * Enqueue scripts for current post_type if necessary.
		 */
		public function enqueue_scripts() {
			$screen = get_current_screen();
			if ( 'post' === $screen->base && ( $lp_id = $this->check_post_type( $screen->post_type ) ) ) {
				$this->enqueue_styles();
				$this->enqueue_js( $lp_id );
			}
		}

		/**
		 * Registers styles.
		 *
		 * @since 1.0.0
		 */
		private function register_styles() {
			wp_register_style( 'lpf_ui', '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/smoothness/jquery-ui.min.css', null );
			wp_register_style( 'lpf_admin_style', CHCH_LIVE_PREVIEW_FREE_URL . 'css/admin.css', array( 'wp-color-picker' ) );
		}

		/**
		 * Registers scripts.
		 *
		 * @since 1.0.0
		 */
		private function register_js() {
			wp_register_script( 'lpf_js', CHCH_LIVE_PREVIEW_FREE_URL . 'js/lp-admin.js', array(
				'jquery',
				'jquery-ui-core',
				'jquery-ui-slider',
				'wp-color-picker',
			) );
		}

		/**
		 * Enqueue styles.
		 *
		 * @since 1.0.0
		 */
		private function enqueue_styles() {
			wp_enqueue_style( 'lpf_ui' );

			wp_enqueue_style( 'lpf_admin_style' );
		}

		/**
		 * Enqueue scripts and localize scripts.
		 *
		 * @param string $lp_id - id of lp instance.
		 *
		 * @since 1.0.0
		 */
		private function enqueue_js( $lp_id ) {

			if ( ! $lp_object = CHCH_LPF_Objects::get_lp_object( $lp_id ) ) {
				return;
			}
			wp_enqueue_media();
			wp_enqueue_script( 'lpf_js' );

			wp_localize_script( 'lpf_js', 'chch_lpf_ajax_object', array(
				'ajaxUrl'           => admin_url( 'admin-ajax.php' ),
				'chch_lpf_tpl_url'  => $lp_object->tpl_url,
				'chch_lpf_base_css' => $lp_object->base_css,
				'chch_lpf_tabs'         => $this->get_tabs( $lp_object ),
			) );
		}

		/**
		 * Add Tabs View
		 *
		 * @since  1.0.0
		 *
		 * @param $lp_object
		 *
		 * @return string - generated tabs html
		 */
		public function get_tabs( $lp_object ) {
			$lp_view = new CHCH_LPF_Views( $lp_object );

			return $lp_view->get_tabs();
		}

		/**
		 * Add Templates View
		 *
		 * @since  0.1.0
		 *
		 * @param $post
		 */
		public function templates_view( $post ) {
			$screen = get_current_screen();

			if ( 'post' == $screen->base && ( $lp_id = $this->check_post_type( $screen->post_type ) ) ) {

				if ( ! $lp_object = CHCH_LPF_Objects::get_lp_object( $lp_id ) ) {
					return;
				}

				$lp_view = new CHCH_LPF_Views( $lp_object );
				$lp_view->get_lp_view();
			}
		}

		/**
		 * Save Post Type Meta
		 *
		 * @since  0.1.0
		 *
		 * @param $post_id
		 * @param $post
		 * @param $update
		 */
		function save_lp_data( $post_id, $post, $update ) {

			if ( ! isset( $_POST['chch_lpf_save_nonce'] ) || ! wp_verify_nonce( $_POST['chch_lpf_save_nonce'], 'chch_lpf_save_nonce_' . $post_id ) ) {
				return;
			}

			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}

			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}

			$screen = get_current_screen();
			if ( 'post' === $screen->base && ( $lp_id = $this->check_post_type( $post->post_type ) ) ) {

				if ( ! $lp_object = CHCH_LPF_Objects::get_lp_object( $lp_id ) ) {
					return;
				}

				if ( isset( $_REQUEST['_chch_lpf_template'] ) || ! empty( $_REQUEST['_chch_lpf_template'] ) ) {
					$data = new CHCH_LPF_Data( $lp_object, $_REQUEST['_chch_lpf_template'], $post_id );
					$data->save_post_fields( $_POST, $post_id );
				}
			}
		}

		/**
		 * Register TinyMce event
		 *
		 * @since     1.0.0
		 *
		 */
		function chch_lp_tinymce_event() {
			if ( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) ) {
				add_filter( 'mce_external_plugins', array( $this, 'chch_lp_tinymce_keyup' ) );
			}
		}

		/**
		 * Add keyup to tineMce for WP version > 3.9
		 *
		 * @since     1.0.0
		 *
		 * @param $plugin_array
		 *
		 * @return
		 */
		function chch_lp_tinymce_keyup( $plugin_array ) {
			$plugin_array['chch_lp_keyup_event'] = CHCH_LIVE_PREVIEW_FREE_URL . 'js/chch-tinymce.js';

			return $plugin_array;
		}

		/**
		 *
		 */
		public function ajax_load_lpf_form() {
			$template = $_POST['template'];
			$lpf_id   = $_POST['lpf_id'];
			$id       = $_POST['post_id'];
			if ( ! $lpf_object = CHCH_LPF_Objects::get_lp_object( $lpf_id ) ) {
				echo 'something wrong with you LP, check configuration';
				die();
			}

			//create a new LP_Views object
			$lp_view = new CHCH_LPF_Views( $lpf_object, $id );
			//get live preview view and form
			$lp_view->get_lp_form( $template );
			die();
		}
	}
}
