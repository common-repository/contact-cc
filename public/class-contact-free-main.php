<?php
namespace ChopChop\ContactFree;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( file_exists( CHCH_CONTACT_FREE_DIR . 'public/includes/class-widget.php' ) ) {
	require_once CHCH_CONTACT_FREE_DIR . 'public/includes/class-widget.php';
}

use ChopChop\ContactFormFree\ContactForm;
use ChopChop\LivePreviewFree\CHCH_LPF;
use ChopChop\LivePreviewFree\CHCH_LPF_Templates;
use ChopChop\LivePreviewFree\CHCH_LPF_Objects;

/**
 * Contact Free CC
 *
 * @package   ContactFreeCC
 * @author    Chop-Chop.org <shop@chop-chop.org>
 * @license   GPL-2.0+
 * @link      https://shop.chop-chop.org
 * @copyright 2014
 */

/**
 * @package Contact_Free_Main
 * @author  Chop-Chop.org <shop@chop-chop.org>
 */
class Contact_Free_Main {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '1.0.0';

	/**
	 *Plugin slug used for CPT and scripts
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'chch-contact-free';
	protected $plugin_name = 'Contact CC Free';

	/**
	 *
	 * @since    1.0.0
	 *
	 * @var      array
	 */
	private $contacts = array();

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		add_action( 'chch_lpf_init', array( $this, 'chch_contact_free_live_preview' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'template_scripts_and_styles' ) );
		add_action( 'chch_lp_css_after', array( $this, 'chch_contact_free_custom_css' ), 10, 1 );

		add_shortcode( 'chch_contact_free', array( $this, 'contact' ) );
	}

	/**
	 * Register live preview fields
	 *
	 * @uses      CHCH_LPF
	 * @since     1.0.0
	 */
	function chch_contact_free_live_preview() {

		// Templates dir and url
		$tpl_dir = CHCH_CONTACT_FREE_DIR . 'public/templates/templates/';
		$tpl_url = CHCH_CONTACT_FREE_URL . 'public/templates/templates/';

		$lp_config = array(
			'id'                       => 'contact_free_templates',
			'title'                    => $this->plugin_name,
			'tpl_dir'                  => $tpl_dir,
			'tpl_url'                  => $tpl_url,
			'target_post_types'        => 'chch-contact-free',
			'load_template_css'        => false,
			'disabled_section_content' => '<a href="http://ch-ch.org/contactpro" target="_blank">Available in PRO</a>',
			'base_css'                 => true,
		);

		$live_preview = new CHCH_LPF( $lp_config );
		$live_preview->add_field_section( array(
			'name' => __( 'Size', $this->plugin_name ),
			'id'   => 'size',
		) );

		$live_preview->add_fields_group( 'size', array(
			'id'    => 'size',
			'title' => __( 'Size', $this->plugin_name ),
		) );

		$header_fields = array(
			array(
				'type'     => 'revealer_group',
				'name'     => 'type',
				'desc'     => 'Set Contact Form Size In:',
				'target'   => '.chch-contact-free__article',
				'options'  => array(
					'pixels'  => 'Pixels',
					'percent' => 'Percent',
				),
				'revaeals' => array(
					array(
						'section_title' => 'Pixels',
						'section_id'    => 'pixels',
						'fields'        => array(
							array(
								'type'   => 'number',
								'name'   => 'width_pixels',
								'target' => '.chch-contact-free__article',
								'attr'   => 'width',
								'desc'   => __( 'Size:', $this->plugin_name ),
								'unit'   => 'px',
							),
						),
					),
					array(
						'section_title' => 'Percents',
						'section_id'    => 'percent',
						'fields'        => array(
							array(
								'type'   => 'slider',
								'name'   => 'width',
								'min'    => '0',
								'max'    => '100',
								'step'   => '1',
								'unit'   => '%',
								'attr'   => 'width',
								'target' => '.chch-contact-free__article',
								'desc'   => __( 'Size:', $this->plugin_name ),
							),
						),
					),
				),
			),
		);

		$live_preview->add_fields( 'size', 'size', $header_fields );

		$live_preview->add_field_section( array(
			'name' => __( 'Background', $this->plugin_name ),
			'id'   => 'background',
		) );

		$live_preview->add_fields_group( 'background', array(
			'id'    => 'background',
			'title' => __( 'Background', $this->plugin_name ),
		) );

		$header_fields = array(
			array(
				'type'   => 'color_picker',
				'name'   => 'color',
				'attr'   => 'background-color',
				'target' => '.chch-contact-free__article',
				'desc'   => __( 'Color:', $this->plugin_name ),
			),
			array(
				'type'     => 'revealer_group',
				'name'     => 'type',
				'desc'     => 'Background Type:',
				'target'   => '.chch-contact-free__article',
				'add_css'  => array(
					array(
						'attr'  => 'background-image',
						'value' => '',
					),
				),
				'options'  => array(
					'no'      => 'No Image',
					'image'   => 'Image',
					'pattern' => 'Pattern',
				),
				'revaeals' => array(
					array(
						'section_title' => 'Background Image',
						'section_id'    => 'image',
						'fields'        => array(
							array(
								'type'    => 'upload',
								'name'    => 'image',
								'target'  => '.chch-contact-free__article',
								'attr'    => 'background-image',
								'add_css' => array(
									array(
										'attr'  => 'background-size',
										'value' => 'cover',
									),
								),
								'desc'    => 'Enter a URL or upload an image:',
							),
						),
					),
					array(
						'section_title' => 'Background Pattern',
						'section_id'    => 'pattern',
						'reset'         => 'background-size',
						'fields'        => array(
							array(
								'type'    => 'upload',
								'name'    => 'pattern',
								'add_css' => array(
									array(
										'attr'  => 'background-size',
										'value' => 'auto',
									),
								),
								'target'  => '.chch-contact-free__article',
								'attr'    => 'background-image',
								'desc'    => 'Enter a URL or upload an image:',
							),
							array(
								'type'    => 'select',
								'name'    => 'repeat',
								'target'  => '.chch-contact-free__article',
								'attr'    => 'background-repeat',
								'desc'    => 'Pattern Repeat:',
								'options' => array(
									'repeat'    => 'Repeat',
									'repeat-x'  => 'Repeat-x',
									'repeat-y'  => 'Repeat-y',
									'no-repeat' => 'No Repeat',
								),
							),
						),
					),
				),
			),
		);

		$live_preview->add_fields( 'background', 'background', $header_fields );

		$live_preview->add_field_section( array(
			'name' => __( 'Fonts and Colors', $this->plugin_name ),
			'id'   => 'fonts_colors',
		) );

		$live_preview->add_fields_group( 'fonts_colors', array(
			'id'       => 'header',
			'title'    => __( 'Header', $this->plugin_name ),
			'disabled' => true,
		) );

		$header_fields = array(
			array(
				'type'    => 'select',
				'name'    => 'font',
				'attr'    => 'font-family',
				'desc'    => __( 'Header Font:', $this->plugin_name ),
				'options' => array(
					'Open Sans' => 'Open Sans',
				),
			),
			array(
				'type' => 'color_picker',
				'name' => 'color',
				'attr' => 'color',
				'desc' => __( 'Header Color:', $this->plugin_name ),
			),
		);

		$live_preview->add_fields( 'fonts_colors', 'header', $header_fields );

		$live_preview->add_fields_group( 'fonts_colors', array(
			'id'       => 'subheader',
			'title'    => __( 'Subheader', $this->plugin_name ),
			'disabled' => true,
		) );

		$header_fields = array(
			array(
				'type'   => 'select',
				'name'   => 'font',
				'attr'   => 'font-family',
				'desc'   => __( 'Subheader Font:', $this->plugin_name ),
				'options' => array(
					'Open Sans' => 'Open Sans',
				),
			),
			array(
				'type' => 'color_picker',
				'name' => 'color',
				'attr' => 'color',
				'desc' => __( 'Subheader Color:', $this->plugin_name ),
			),
		);

		$live_preview->add_fields( 'fonts_colors', 'subheader', $header_fields );

		$live_preview->add_fields_group( 'fonts_colors', array(
			'id'       => 'main_content',
			'title'    => __( 'Main Content', $this->plugin_name ),
			'disabled' => true,
		) );

		$header_fields = array(
			array(
				'type'    => 'select',
				'name'    => 'font',
				'attr'    => 'font-family',
				'desc'    => __( 'Main Content Font:', $this->plugin_name ),
				'options' => array(
					'Open Sans' => 'Open Sans',
				),
			),
			array(
				'type' => 'color_picker',
				'name' => 'color',
				'attr' => 'color',
				'desc' => __( 'Main Content Color:', $this->plugin_name ),
			),
		);

		$live_preview->add_fields( 'fonts_colors', 'main_content', $header_fields );

		$live_preview->add_fields_group( 'fonts_colors', array(
			'id'       => 'link',
			'title'    => __( 'Link', $this->plugin_name ),
			'disabled' => true,
		) );

		$header_fields = array(
			array(
				'type'   => 'select',
				'name'   => 'font',
				'attr'   => 'font-family',
				'desc'   => __( 'Link Font:', $this->plugin_name ),
				'options' => array(
					'Open Sans' => 'Open Sans',
				),
			),
			array(
				'type' => 'color_picker',
				'name' => 'color',
				'attr' => 'color',
				'desc' => __( 'Link Color:', $this->plugin_name ),
			),
		);

		$live_preview->add_fields( 'fonts_colors', 'link', $header_fields );

		$live_preview->add_field_section( array(
			'name' => __( 'Inputs and Buttons', $this->plugin_name ),
			'id'   => 'inputs_buttons',
		) );

		$live_preview->add_fields_group( 'inputs_buttons', array(
			'id'       => 'label',
			'title'    => __( 'Label', $this->plugin_name ),
			'disabled' => true,
		) );

		$header_fields = array(
			array(
				'type'   => 'select',
				'name'   => 'font',
				'attr'   => 'font-family',
				'desc'   => __( 'Label Font:', $this->plugin_name ),
				'options' => array(
					'Open Sans' => 'Open Sans',
				),
			),
			array(
				'type' => 'color_picker',
				'name' => 'color',
				'attr' => 'color',
				'desc' => __( 'Label Color:', $this->plugin_name ),
			),
		);

		$live_preview->add_fields( 'inputs_buttons', 'label', $header_fields );

		$live_preview->add_fields_group( 'inputs_buttons', array(
			'id'       => 'input',
			'title'    => __( 'Input', $this->plugin_name ),
			'disabled' => true,
		) );

		$header_fields = array(
			array(
				'type'    => 'class_switcher',
				'name'    => 'grid',
				'desc'    => 'Inputs Grid:',
				'options' => array(
					'chch-contact-free-grid__full' => 'Full Width',
					'chch-contact-free-grid__half' => 'Half Width',
					// 'chch-contact-free-grid__one'  => 'Single',
				),
			),
			array(
				'type'     => 'select',
				'name'     => 'font',
				'attr'     => 'font-family',
				'pseudo'   => 'placeholder',
				'multiple' => 'selectors',
				'desc'     => __( 'Input Font:', $this->plugin_name ),
				'options' => array(
					'Open Sans' => 'Open Sans',
				),
			),
			array(
				'type'     => 'color_picker',
				'name'     => 'color',
				'attr'     => 'color',
				'multiple' => 'selectors',
				'desc'     => __( 'Input Text Color:', $this->plugin_name ),
			),

			array(
				'type'        => 'color_picker',
				'name'        => 'placeholder_color',
				'attr'        => 'color',
				'pseudo'      => 'placeholder',
				'only_pseudo' => true,
				'multiple'    => 'selectors',
				'desc'        => __( 'Placeholder Color:', $this->plugin_name ),
			),

			array(
				'type'     => 'slider',
				'name'     => 'radius',
				'min'      => '0',
				'max'      => '50',
				'step'     => '1',
				'unit'     => 'px',
				'attr'     => 'border-radius',
				'multiple' => 'selectors',
				'desc'     => __( 'Input Border Radius:', $this->plugin_name ),
			),
		);

		$live_preview->add_fields( 'inputs_buttons', 'input', $header_fields );

		$live_preview->add_fields_group( 'inputs_buttons', array(
			'id'       => 'button',
			'title'    => __( 'button', $this->plugin_name ),
			'disabled' => true,
		) );

		$header_fields = array(
			array(
				'type'   => 'select',
				'name'   => 'font',
				'attr'   => 'font-family',
				'desc'   => __( 'Button Font:', $this->plugin_name ),
				'options' => array(
					'Open Sans' => 'Open Sans',
				),
			),
			array(
				'type' => 'color_picker',
				'name' => 'color',
				'attr' => 'color',
				'desc' => __( 'Button Text Color:', $this->plugin_name ),
			),
			array(
				'type' => 'slider',
				'name' => 'radius',
				'min'  => '0',
				'max'  => '50',
				'step' => '1',
				'unit' => 'px',
				'attr' => 'border-radius',
				'desc' => __( 'Button Border Radius:', $this->plugin_name ),
			),
			array(
				'type' => 'color_picker',
				'name' => 'background',
				'attr' => 'background-color',
				'desc' => __( 'Button Background Color:', $this->plugin_name ),
			),
		);

		$live_preview->add_fields( 'inputs_buttons', 'button', $header_fields );

		$live_preview->add_field_section( array(
			'name' => __( 'Content', $this->plugin_name ),
			'id'   => 'content',
		) );

		$live_preview->add_fields_group( 'content', array(
			'id'    => 'content',
			'title' => __( 'Content', $this->plugin_name ),
		) );

		$general_fields = array(
			array(
				'type'   => 'editor',
				'name'   => 'header',
				'target' => '.chch-contact-free__content--header',
				'desc'   => __( 'Form Title:', $this->plugin_name ),
			),
			array(
				'type'   => 'editor',
				'name'   => 'subheader',
				'target' => '.chch-contact-free__content--subheader',
				'html'   => 'no',
				'desc'   => __( 'Form Description:', $this->plugin_name ),
			),
			array(
				'type'   => 'editor',
				'name'   => 'content',
				'target' => '.chch-contact-free__content--content',
				'html'   => 'no',
				'desc'   => __( 'Form Description:', $this->plugin_name ),
			),
			array(
				'type'   => 'text',
				'name'   => 'text',
				'attr'   => 'value',
				'target' => '.chch-contact-free__btn',
				'desc'   => 'Button Text:',
			),
		);

		$live_preview->add_fields( 'content', 'content', $general_fields );
	}

	/**
	 * Print user custom css.
	 * Callback for chch_lp_css_after hook
	 *
	 * @param $id
	 */
	function chch_contact_free_custom_css( $id ) {
		if ( $custom_css = get_post_meta( $id, '_chch_contact_free_css', true ) ) {
			echo $custom_css;
		}
	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 * @return    string Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Return the plugin name.
	 *
	 * @since    1.0.0
	 *
	 * @return    string Plugin name variable.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean $network_wide       True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();

					restore_current_blog();
				}
			} else {
				self::single_activate();
			}
		} else {
			self::single_activate();
		}
	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean $network_wide       True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

					restore_current_blog();
				}
			} else {
				self::single_deactivate();
			}
		} else {
			self::single_deactivate();
		}
	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0.0
	 *
	 * @param    int $blog_id ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();
	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    0.1.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );
	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	private static function single_activate() {
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	private static function single_deactivate() {
	}

	/**
	 * Get LP template view
	 *
	 * @since     1.0.0
	 * @uses      \CHCH_LIVE_PREVIEW
	 * @uses      \LP_Templates
	 *
	 * @param $id
	 *
	 * @return string
	 */
	public static function show_contacts( $id ) {

		ob_start();
		$lp_object = CHCH_LPF_Objects::get_lp_object( 'contact_free_templates' );

		if ( $lp_object instanceof \ChopChop\LivePreviewFree\CHCH_LPF ) {
			$show_on_mobile = get_post_meta( $id, '_chch_contact_free_show_on_mobile', true );
			$show_on_mobile = $show_on_mobile ? 'contact-free-mobile-hide' : '';
			echo '<div id="contact-free-' . $id . '" class="' . $show_on_mobile . '">';
			$template_id = get_post_meta( $id, '_chch_lpf_template', true );
			$template    = new CHCH_LPF_Templates( $lp_object, $template_id, $id );

			$template->get_template();
			$template->build_css( '#contact-free-' . $id );
			echo '</div>';
		}
		$content = ob_get_clean();

		return $content;
	}

	/**
	 * Callback for add_shortcode function
	 *
	 * @param $atts
	 *
	 * @return string
	 */
	function contact( $atts ) {
		$atts = shortcode_atts( array(
			'id' => 0,
		), $atts, 'contact' );

		if ( $atts['id'] != 0 && get_post_status( $atts['id'] ) == 'publish' ) {
			return $this->show_contacts( $atts['id'] );
		} else {
			return '';
		}
	}

	function get_newsletter_form( $post_id, $lp ) {
		$form = new ContactForm( $post_id );
		$form->get_form( $lp->get_template_option( 'content', 'text' ) );
	}

	/**
	 * Get All Active Contact Forms
	 *
	 * @since  1.0.0
	 *
	 * @return   array
	 */
	public function get_contact_forms() {
		$list = array();

		$args = array(
			'post_type'      => $this->plugin_slug,
			'posts_per_page' => - 1,
			'post_status'    => 'publish',
		);

		$contact_forms = get_posts( $args );

		if ( $contact_forms ) {
			foreach ( $contact_forms as $contact_form ) {
				$list[] = $contact_form->ID;
			}
		}

		return $list;
	}

	/**
	 * Include Templates scripts and styles on Front-End
	 *
	 * @since  1.0.0
	 */
	function template_scripts_and_styles() {

		$contacts = $this->get_contact_forms();

		if ( file_exists( CHCH_CONTACT_FREE_DIR . 'public/templates/css/public.css' ) ) {
			wp_enqueue_style( $this->plugin_slug . '_template_defaults', CHCH_CONTACT_FREE_URL . 'public/templates/css/public.css', null, Contact_Free_Main::VERSION, 'all' );
		}

		foreach ( $contacts as $id ) {

			$template_id = get_post_meta( $id, '_chch_lpf_template', true );

			$lp_object = CHCH_LPF_Objects::get_lp_object( 'contact_free_templates' );

			if ( $lp_object instanceof \ChopChop\LivePreviewFree\CHCH_LPF ) {
				$template = new CHCH_LPF_Templates( $lp_object, $template_id, $id );

				$template->include_fonts();
			}
		}
	}
}
