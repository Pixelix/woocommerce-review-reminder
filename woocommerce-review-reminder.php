<?php
/*
 * Plugin Name: WooCommerce Review Reminder
 * Plugin URI: http://wordpress.org/plugins/woocommerce-review-reminder/
 * Description: Reminder to leave feedback about a purchase.
 * Version: 1.1
 * Author: Pixelix
 * Author URI: http://pixelix.ru
 * License: GPLv2
 * License URI: ttp://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: woocommerce-review-reminder
 * Domain Path: /lang/
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Include the actions.
 */
require_once( plugin_dir_path( __FILE__ ) . 'includes/class-wc-review-reminder.php' );

/**
 * Activation and deactivation methods.
 */
register_activation_hook( __FILE__, array( 'WC_Review_Reminder', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'WC_Review_Reminder', 'deactivate' ) );

/**
 * Initializes the actions.
 */
add_action( 'plugins_loaded', array( 'WC_Review_Reminder', 'get_instance' ) );

/**
 * Includes and initializes the plugin admin.
 */
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
	require_once( plugin_dir_path( __FILE__ ) . 'includes/admin/class-wc-review-reminder-admin.php' );
	add_action( 'plugins_loaded', array( 'WC_Review_Reminder_Admin', 'get_instance' ) );
}
