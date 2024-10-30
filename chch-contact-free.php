<?php
/**
 * DESC
 *
 * @package   ContactFreeCC
 * @author    Chop-Chop.org <shop@chop-chop.org>
 * @license   GPL-2.0+
 * @link      https://shop.chop-chop.org
 * @copyright 2014
 *
 * @wordpress-plugin
 * Plugin Name:       Contact Free CC
 * Plugin URI:        http://shop.chop-chop.org
 * Description:       An elegant Contact Form in just a few clicks.
 * Version:           1.0.2
 * Author:            Chop-Chop.org
 * Author URI:        http://chop-chop.org
 * Text Domain:       cc-contact-locale
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( version_compare( PHP_VERSION, '5.3', '<' ) ) {
	add_action( 'admin_notices', function () {
		echo '<div class="error" style="background: rgba(221, 61, 54, 0.2); padding: 10px;   border: 2px solid #dd3d36;"><p>. Contact Free CC Time requires PHP 5.3 to function properly. Please upgrade PHP or deactivate Contact Free CC Time.</p></div>';
	} );

	return;
} else {

	define( 'CHCH_CONTACT_FREE_URL', plugin_dir_url( __FILE__ ) );
	define( 'CHCH_CONTACT_FREE_DIR', plugin_dir_path( __FILE__ ) );

	/*
	 * Public-Facing Functionality
	 */

	if ( file_exists( CHCH_CONTACT_FREE_DIR . 'includes/chch-live-preview-free/class-chch-lpf-init.php' ) ) {
		require_once( CHCH_CONTACT_FREE_DIR . 'includes/chch-live-preview-free/class-chch-lpf-init.php' );
	}

	if ( file_exists( CHCH_CONTACT_FREE_DIR . 'includes/ContactFree/chch-contact-init.php' ) ) {
		require_once( CHCH_CONTACT_FREE_DIR . 'includes/ContactFree/chch-contact-init.php' );
	}

	if ( file_exists( CHCH_CONTACT_FREE_DIR . 'public/class-contact-free-main.php' ) ) {
		require_once( CHCH_CONTACT_FREE_DIR . 'public/class-contact-free-main.php' );
	}

	/*
	 * Register hooks that are fired when the plugin is activated or deactivated.
	 * When the plugin is deleted, the uninstall.php file is loaded.
	 */
	register_activation_hook( __FILE__, array( 'ChopChop\ContactFree\Contact_Free_Main', 'activate' ) );
	register_deactivation_hook( __FILE__, array( 'ChopChop\ContactFree\Contact_Free_Main', 'deactivate' ) );

	add_action( 'plugins_loaded', array( 'ChopChop\ContactFree\Contact_Free_Main', 'get_instance' ) );

	/**
	 * Dashboard and Administrative Functionality
	 */
	if ( is_admin() ) {
		include_once( plugin_dir_path( __FILE__ ) . 'admin/includes/chch-repeater/chch-repeater-init.php' );

		require_once( plugin_dir_path( __FILE__ ) . 'admin/class-contact-free-admin.php' );
		add_action( 'plugins_loaded', array( 'ChopChop\\ContactFree\\Contact_Free_Admin', 'get_instance' ) );
	}
}
