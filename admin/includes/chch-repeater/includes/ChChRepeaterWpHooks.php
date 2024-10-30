<?php

namespace ChopChop\Repeater;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class ChChRepeaterWpHooks {

	/**
	 * Instance of ChChRepeaterWpHooks class
	 *
	 * @var object - ChChRepeaterWpHooks
	 */
	public static $instance = null;

	/**
	 * Setup class
	 */
	public function __construct() {
		if ( is_admin() && current_user_can('edit_posts')) {
			$this->admin_hooks();
		}
	}

	/**
	 * Make it singleton.
	 *
	 * @since  1.0.0
	 * @return ChChRepeaterWpHooks single instance object
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 *	Register wordpress admin hooks.
	 */
	public function admin_hooks() {

		add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_action( 'wp_ajax_chch_lp_load_lp_form', array( $this, 'ajax_load_lp_form' ) );
	}

	/**
	 * Wrapper for register scripts and styles.
	 */
	public function register_scripts() {
		$this->register_styles();
		$this->register_js();
	}

	/**
	 * Enqueue scripts if current post-type is registered in lp object.
	 */
	public function enqueue_scripts() {
		$screen = get_current_screen();
		if ( 'post' == $screen->base && ( $lp_id = $this->check_post_type( $screen->post_type ) ) ) {
			$this->enqueue_styles();
			$this->enqueue_js( $lp_id );
		}
	}

	/**
	 * Register styles for admin.
	 *
	 * @since 1.0.0
	 */
	private function register_styles() {
		wp_register_style( 'lp_ui', '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/smoothness/jquery-ui.min.css', null );
		wp_register_style( 'lp_admin_style', CHCH_LIVE_PREVIEW_URL . 'css/admin.css', array( 'wp-color-picker' ) );
	}

	/**
	 * Register script for admin.
	 *
	 * @since 1.0.0
	 */
	private function register_js() {
		wp_register_script( 'lp_js', CHCH_LIVE_PREVIEW_URL . 'js/lp-admin.js', array(
			'jquery',
			'jquery-ui-core',
			'jquery-ui-slider',
			'wp-color-picker',
		) );
	}

	/**
	 * Enqueue styles for admin.
	 *
	 */
	private function enqueue_styles() {
		wp_enqueue_style( 'lp_ui' );

		return wp_enqueue_style( 'lp_admin_style' );
	}

	/**
	 * Enqueue scripts for admin.
	 *
	 * @param $lp_id
	 */
	private function enqueue_js( $lp_id ) {

		if ( !$lp_object = LP_Objects::get_lp_object( $lp_id ) ) {
			return;
		}
		wp_enqueue_media();
		wp_enqueue_script( 'lp_js' );

		wp_localize_script( 'lp_js', 'chch_lp_ajax_object', array(
			'ajaxUrl'              => admin_url( 'admin-ajax.php' ),
			'chch_lp_tpl_url'      => $lp_object->get_param( 'tpl_url' ),
			'load_template_css'    => $lp_object->get_param( 'base_css' ),
			'chch_lp_tabs'         => $this->get_tabs( $lp_object ),
			'multiple_fields_sets' => $lp_object->get_param( 'multiple_fields_sets' ),
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
		$lp_view = new LP_Views( $lp_object );

		return $lp_view->get_tabs();
	}

	/**
	 * Add Templates View
	 *
	 * @since  1.0.0
	 */
	public function templates_view() {
		$screen = get_current_screen();

		if ( 'post' == $screen->base && ( $lp_id = $this->check_post_type( $screen->post_type ) ) ) {

			if ( !$lp_object = LP_Objects::get_lp_object( $lp_id ) ) {
				return;
			}

			$lp_view = new LP_Views( $lp_object );
			$lp_view->get_lp_view();
		}
	}

	/**
	 * Save Post Type Meta
	 *
	 * @since  1.0.0
	 */
	function save_lp_data( $post_id, $post, $update ) {

		if ( !isset( $_POST[ 'chch_lp_save_nonce' ] ) || !wp_verify_nonce( $_POST[ 'chch_lp_save_nonce' ], 'chch_lp_save_nonce_' . $post_id ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( !current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( 'post' == $screen->base && ( $lp_id = $this->check_post_type( $post->post_type ) ) ) {

			if ( !$lp_object = LP_Objects::get_lp_object( $lp_id ) ) {
				return;
			}

			if ( isset( $_POST[ '_chch_lp_template' ] ) || !empty( $_POST[ '_chch_lp_template' ] ) ) {
				$data = new LP_Data( $lp_object, $_REQUEST[ '_chch_lp_template' ], $post_id );
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
	 */
	function chch_lp_tinymce_keyup( $plugin_array ) {
		$plugin_array[ 'chch_lp_keyup_event' ] = CHCH_LIVE_PREVIEW_URL . 'js/chch-tinymce.js';

		return $plugin_array;
	}

	/**
	 * Ajax function - load lp view after click customize button.
	 */
	public function ajax_load_lp_form() {
		$lp_id = $_POST[ 'lp_id' ];
		if ( !$lp_object = LP_Objects::get_lp_object( $lp_id ) ) {
			echo 'something wrong with you LP, check configuration';
			die();
		}

		$template = $_POST[ 'template' ];
		$set = '';
		if ( isset( $_POST[ 'templateSet' ] ) ) {
			$set = $_POST[ 'templateSet' ];
		}

		$id = $_POST[ 'post_id' ];

		//create a new LP_Views object
		$lp_view = new LP_Views( $lp_object, $id );
		//get live preview view and form
		$lp_view->get_lp_form( $template, $set );
		die();
	}
}