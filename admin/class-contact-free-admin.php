<?php
namespace ChopChop\ContactFree;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
use ChopChop\Repeater\ChChRepeater;

if ( file_exists( CHCH_CONTACT_FREE_DIR . 'admin/includes/CMB2/init.php' ) ) {
	require_once CHCH_CONTACT_FREE_DIR . 'admin/includes/CMB2/init.php';
}

if ( file_exists( CHCH_CONTACT_FREE_DIR . 'admin/includes/ContactProView.php' ) ) {
	require_once CHCH_CONTACT_FREE_DIR . 'admin/includes/ContactProView.php';
}

/**
 * @package CcPopUpProTime
 * @author  Chop-Chop.org <shop@chop-chop.org>
 */
class Contact_Free_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Contact_Free_Admin constructor.
	 */
	private function __construct() {
		$this->plugin      = Contact_Free_Main::get_instance();
		$this->plugin_slug = $this->plugin->get_plugin_slug();
		$this->plugin_name = $this->plugin->get_plugin_name();

		add_action( 'init', array( $this, 'register_post_type' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_filter( 'chch-lpf-tabs-contact_free_templates', array( $this, 'contact_free_lp_tabs' ), 10, 1 );
		add_action( 'cmb2_admin_init', array( $this, 'add_fields' ) );
		add_action( 'add_meta_boxes_' . $this->plugin_slug, array( $this, 'process_meta_boxes' ) );
		add_action( "save_post_{$this->plugin_slug}", array( $this, 'save_fields' ), 10, 1 );
		add_action( 'admin_head', array( $this, 'admin_head_scripts' ) );

		add_action( 'media_buttons_context', array( $this, 'add_shortcocde_button' ) );
		add_action( 'admin_footer', array( $this, 'add_shortcode_modal' ) );
	}

	/**
	 * Add shortcode button to wordpress editor
	 *
	 * @param array $buttons
	 *
	 * @return array $buttons
	 */
	function add_shortcocde_button( $buttons ) {
		$screen = get_current_screen();
		if ( $screen->post_type != $this->plugin_slug ) {
			$button_icon = CHCH_CONTACT_FREE_URL . 'admin/assets/img/icon.png';
			echo '<a href="#TB_inline?width=200&height=200&inlineId=chch-contact-free-list" id="chch-contact-free-list-modal-trigger" class="button thickbox"><span class="wp-media-buttons-icon" style="background: url(' . $button_icon . ');background-repeat: no-repeat; background-position: left bottom;"></span>Add Contact PRO CC</a>';
		}
	}

	/**
	 *
	 */
	function add_shortcode_modal() {

		$contact_forms = $this->plugin->get_contact_forms();
		$modal         = '<div id="chch-contact-free-list" style="display:none;"><p>
          <select id="chch-contact-free-list-select">';
		foreach ( $contact_forms as $contact_form ) {
			$title              = get_the_title( $contact_form );
			$contact_form_title = $title ? $title : __( 'Conact Form ' . $contact_form, $this->plugin_slug );
			$modal .= '<option value="' . $contact_form . '">' . $contact_form_title . '</option>';
		}

		$modal .= '</select> <a class="button" href="#" id="chch-contact-free-insert-shortcode">Insert shortcode</a></p> </div>';
		$modal .= ' <script type="text/javascript">
				jQuery(document).ready(function($) {
				  $("#chch-contact-free-insert-shortcode").on("click", function() {
				  var contact_id = $("#chch-contact-free-list-select option:selected").val();
					if(window.parent.tinyMCE && window.parent.tinyMCE.activeEditor)
					{
						window.parent.send_to_editor("[chch_contact_free id="+contact_id+"]");
					}
					tb_remove();
				  })
				});
</script>';
		echo $modal;
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
	 * Register custom post type.
	 */
	function register_post_type() {

		$labels = array(
			'name'               => _x( 'Contact CC Free', 'Post Type General Name', $this->plugin_name ),
			'singular_name'      => _x( 'Contact CC Free', 'Post Type Singular Name', $this->plugin_name ),
			'menu_name'          => __( 'Contact CC Free', $this->plugin_name ),
			'parent_item_colon'  => __( 'Parent Item:', $this->plugin_name ),
			'all_items'          => __( 'Contacts CC Free', $this->plugin_name ),
			'view_item'          => __( 'View Item', $this->plugin_name ),
			'add_new_item'       => __( 'Add New Contact CC Free', $this->plugin_name ),
			'add_new'            => __( 'Add New Contact CC Free', $this->plugin_name ),
			'edit_item'          => __( 'Edit Contact CC Free', $this->plugin_name ),
			'update_item'        => __( 'Update Contact CC Free', $this->plugin_name ),
			'search_items'       => __( 'Search Contact CC Free', $this->plugin_name ),
			'not_found'          => __( 'Not found', $this->plugin_name ),
			'not_found_in_trash' => __( 'No Contact CC Free found in Trash', $this->plugin_name ),
		);

		$args = array(
			'label'               => __( 'Contact Free', $this->plugin_name ),
			'description'         => __( '', $this->plugin_name ),
			'labels'              => $labels,
			'supports'            => array( 'title' ),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => false,
			'menu_position'       => 65,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'rewrite'             => false,
		);
		register_post_type( $this->plugin_slug, $args );
	}

	function register_scripts() {
		wp_register_style( $this->plugin_slug . '-admin', CHCH_CONTACT_FREE_URL . 'admin/assets/css/admin.css', array(), Contact_Free_Main::VERSION );
		wp_register_style( $this->plugin_slug . '-fonts', CHCH_CONTACT_FREE_URL . 'public/templates/css/public.css', array(), Contact_Free_Main::VERSION, 'all' );
		wp_register_script( $this->plugin_slug . '-admin', CHCH_CONTACT_FREE_URL . 'admin/assets/js/admin.js', array(), Contact_Free_Main::VERSION, true );
		wp_localize_script( $this->plugin_slug . '-admin', 'chch_contact_free', array( 'ajaxUrl' => admin_url( 'admin-ajax.php' ) ) );
	}

	function enqueue_scripts() {

		$screen = get_current_screen();
		if ( 'post' == $screen->base && $this->plugin_slug === $screen->post_type ) {
			wp_enqueue_script( $this->plugin_slug . '-admin' );
			wp_enqueue_style( $this->plugin_slug . '-admin' );
			wp_enqueue_style( $this->plugin_slug . '-fonts' );

			ChChRepeater::enqueue_scripts();
		}
	}

	/**
	 * Add tabs to cpt edit screen.
	 * Callback for chch-lpf-tabs-contact_free_templates filters
	 *
	 * @see LP_Views
	 *
	 * @param $tabs
	 *
	 * @return mixed
	 */
	function contact_free_lp_tabs( $tabs ) {

		$tabs['contact_type'] = array(
			'id'       => 'contact_type',
			'name'     => 'Contact Type',
			'priority' => 11,
			'icon'     => 'admin-tools',
		);

		return $tabs;
	}

	/**
	 * Save post meta.
	 * Callback for save_post action.
	 *
	 * @param $post_id
	 *
	 */
	function save_fields( $post_id ) {

		$options_slug = '_chch_contact_free_';

		if ( isset( $_POST[ $options_slug . 'contact_type' ] ) ) {
			$type = sanitize_text_field( $_POST[ $options_slug . 'contact_type' ] );
			update_post_meta( $post_id, $options_slug . 'contact_type', sanitize_text_field( $type ) );
			switch ( $type ) {
				case 'email':
					$email_data = array();
					if ( isset( $_POST[ $options_slug . 'email_address' ] ) ) {
						$email = sanitize_email($_POST[ $options_slug . 'email_address' ]);
						$email_data['email_address'] = $email;
					}

					if ( isset( $_POST[ $options_slug . 'subject' ] ) ) {
						$subject = sanitize_text_field($_POST[ $options_slug . 'subject' ]);
						$email_data['subject'] = $subject;
					}
					if ( isset( $_POST[ $options_slug . 'message_body' ] ) ) {
						$message =  esc_textarea($_POST[ $options_slug . 'message_body' ]);
						$email_data['message_body'] = $message;
					}

					if ( isset( $_POST[ $options_slug . 'email_fields' ] ) ) {
						$fields = (array)$_POST[ $options_slug . 'email_fields' ];
						$email_data['fields'] = $fields;
					}

					update_post_meta( $post_id, 'email_data', $email_data );
				break;
			}
		}
	}

	function add_fields() {

		$prefix = '_chch_contact_free_';

		$general_metabox = new_cmb2_box( array(
			'id'           => $prefix . 'general',
			'title'        => __( 'General', $this->plugin_name ),
			'object_types' => array( $this->plugin_slug ),
		) );

		$general_metabox->add_field( array(
			'name'    => __( 'Contact Form Status', $this->plugin_name ),
			'desc'    => __( 'Enable or disable the plugin.', $this->plugin_name ),
			'id'      => $prefix . 'status',
			'type'    => 'radio_inline',
			'default' => 'yes',
			'options' => array(
				'yes' => __( 'Turned ON', $this->plugin_name ),
				'no'  => __( 'Turned OFF', $this->plugin_name ),
			),
		) );

		$general_metabox->add_field( array(
			'name' => __( 'Hide on Mobile Devices?', $this->plugin_name ),
			'desc' => __( 'The contact form will not be visible on mobile devices.', $this->plugin_name ),
			'id'   => $prefix . 'show_on_mobile',
			'type' => 'checkbox',
		) );

		$messages_metabox = new_cmb2_box( array(
			'id'           => $prefix . 'messages',
			'title'        => __( 'Messages', $this->plugin_name ),
			'object_types' => array( $this->plugin_slug ),
			'priority'     => 'low',
		) );

		$messages_metabox->add_field( array(
			'name'    => __( 'Success Message:', $this->plugin_name ),
			'id'      => $prefix . 'message_success',
			'type'    => 'text',
			'default' => __( 'Thank You!', $this->plugin_name ),
		) );

		$messages_metabox->add_field( array(
			'name'    => __( 'Send Error Message:', $this->plugin_name ),
			'id'      => $prefix . 'message_failed',
			'type'    => 'text',
			'default' => __( 'Something went wrong!', $this->plugin_name ),
		) );

		$messages_metabox->add_field( array(
			'name'    => __( 'Wrong Format Error:', $this->plugin_name ),
			'id'      => $prefix . 'message_format_error',
			'type'    => 'text',
			'default' => __( 'This value is invalid.', $this->plugin_name ),
		) );

		$messages_metabox->add_field( array(
			'name'    => __( 'Required Field Error:', $this->plugin_name ),
			'id'      => $prefix . 'message_req_error',
			'type'    => 'text',
			'default' => __( 'This field is mandatory.', $this->plugin_name ),
		) );
	}

	function process_meta_boxes() {
		remove_meta_box( 'slugdiv', $this->plugin_slug, 'normal' );

		$prefix = '_chch_contact_free_';

		add_meta_box( $prefix . 'contact-type', __( 'Contact Type', $this->plugin_slug ), array(
			$this,
			'render_contact_type_metabox',
		), $this->plugin_slug, 'normal', 'high' );
		add_meta_box( $prefix . 'shortcode', __( 'Shortcode', $this->plugin_slug ), array(
			$this,
			'render_shortcode_metabox',
		), $this->plugin_slug, 'side', 'low' );

		add_filter( 'postbox_classes_' . $this->plugin_slug . '_' . $prefix . 'contact-type', function ( $classes ) {
			array_push( $classes, 'chch-lpf-tab chch-lpf-tab-contact_type' );

			return $classes;
		} );

		add_filter( 'postbox_classes_' . $this->plugin_slug . '_' . $prefix . 'messages', function ( $classes ) {
			array_push( $classes, 'chch-lpf-tab chch-lpf-tab-contact_type' );

			return $classes;
		} );

		add_filter( 'postbox_classes_' . $this->plugin_slug . '_' . $prefix . 'general', function ( $classes ) {
			array_push( $classes, 'chch-lpf-tab chch-lpf-tab-settings' );

			return $classes;
		} );
		add_filter( 'postbox_classes_' . $this->plugin_slug . '_' . $prefix . 'advanced', function ( $classes ) {
			array_push( $classes, 'chch-lpf-tab chch-lpf-tab-advanced' );

			return $classes;
		} );
	}

	/**
	 * View for shortcode metabox
	 * Callback for add_meta_box().
	 *
	 * @param $post
	 */
	function render_shortcode_metabox( $post ) {
		echo '<p class="cmb_metabox_description cmb_metabox_description-full">Use the following shortcode to display this contact form inside a post, page or text widget.
		:<br /><br /><code>[chch_contact_free id=' . $post->ID . ']</code></p>';
		// echo '<code>[chch_contact_free id=' . $post->ID . ']</code>';

	}

	/**
	 * Includes Contact Type metabox view.
	 * Callback for add_meta_box().
	 *
	 * @param $post
	 */
	function render_contact_type_metabox( $post ) {
		$view_file = CHCH_CONTACT_FREE_DIR . '/admin/views/metabox/contact-metabox.php';
		if ( file_exists( $view_file ) ) {
			include_once( $view_file );
		}
	}

	/**
	 * Gets repeater with given id and fields.
	 *
	 * @param $field_set  - set of fields
	 * @param $id         - repeater id
	 * @param $saved_data - values of saved fields, default false
	 *
	 * @uses ChChRepeater
	 */
	private function get_fields_repeater( $field_set, $id, $saved_data = false ) {
		$fields = array(
			'email' => array(
				array(
					'type'    => 'revealer_select',
					'id'      => 'type',
					'name'    => __( 'Field Type:', $this->plugin_slug ),
					'options' => array(
						'text'     => __( 'Text', $this->plugin_slug ),
						'email'    => __( 'Email', $this->plugin_slug ),
						'phone'    => __( 'Phone', $this->plugin_slug ),
						'number'   => __( 'Number', $this->plugin_slug ),
						'textarea' => __( 'Textarea', $this->plugin_slug ),
						'date'     => __( 'Date', $this->plugin_slug ),
						'select'   => __( 'Dropdown', $this->plugin_slug ),
						'checkbox' => __( 'Checkbox', $this->plugin_slug ),
						'radio'    => __( 'Radio', $this->plugin_slug ),
					),
					'default' => 'email',
				),
				array(
					'id'      => 'id',
					'name'    => __( 'Field ID:', $this->plugin_slug ),
					'type'    => 'text',
					'default' => 'email',
				),
				array(
					'type'    => 'text',
					'id'      => 'label',
					'name'    => __( 'Field Label:', $this->plugin_slug ),
					'default' => 'E-mail',
				),
				array(
					'type'    => 'text',
					'id'      => 'placeholder',
					'name'    => __( 'Field Placeholder:', $this->plugin_slug ),
					'show_if' => 'text, email, phone, number, textarea',
					'default' => 'Your e-mail',
				),
				array(
					'type' => 'checkbox',
					'id'   => 'req',
					'name' => __( 'Required', $this->plugin_slug ),
				),
				array(
					'type'    => 'repeater',
					'id'      => 'options',
					'name'    => __( 'Field Options:', $this->plugin_slug ),
					'show_if' => 'select, radio',
					'fields'  => array(
						array(
							'id'   => 'value',
							'name' => __( 'Value:', $this->plugin_slug ),
							'type' => 'text',
						),
						array(
							'id'         => 'selected',
							'name'       => __( 'Selected:', $this->plugin_slug ),
							'group_rows' => true,
							'type'       => 'radio',
						),
					),
				),
				array(
					'type'    => 'repeater',
					'id'      => 'checkbox_options',
					'name'    => __( 'Field Options:', $this->plugin_slug ),
					'show_if' => 'checkbox',
					'fields'  => array(
						array(
							'id'   => 'value',
							'name' => __( 'Value:', $this->plugin_slug ),
							'type' => 'text',
						),
						array(
							'id'   => 'selected',
							'name' => __( 'Selected', $this->plugin_slug ),
							'type' => 'checkbox',
						),
					),
				),
			),
		);

		$repeater_fields = $fields['email'];

		if ( isset( $fields[ $field_set ] ) ) {
			$repeater_fields = $fields[ $field_set ];
		}

		$repeater = new ChChRepeater( $id, $repeater_fields, $saved_data );
		$repeater->get_repeater();
	}

	/**
	 * Include google fonts
	 *
	 * @since  0.1.0
	 */
	public function admin_head_scripts() {
		$screen = get_current_screen();
		if ( 'post' == $screen->base && $this->plugin_slug === $screen->post_type ) {

			$js = "<link href='//fonts.googleapis.com/css?family=Playfair+Display:400,700,900|Lora:400,700|Open+Sans:400,300,700|Oswald:700,300|Roboto:400,700,300|Signika:400,700,300' rel='stylesheet' type='text/css'>";
			echo $js;
		}
	}
}
