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

function wcrr_init() {
	load_plugin_textdomain( 'woocommerce-review-reminder', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
}
add_action('init', 'wcrr_init');

function wcrr_admin_add_help_tab () {
    global $pixelix_wc_reminder_callback;
    $screen = get_current_screen();

    if ( $screen->id != $pixelix_wc_reminder_callback )
        return;

    $screen->add_help_tab( array(
        'id'	=> 'wcrr_help_tab',
        'title'	=> __('Configuring plugin', 'woocommerce-review-reminder'),
        'content'	=> '<p>' . __( 'You can ask your plugin settings. If any of the fields will be empty, the default value is used.', 'woocommerce-review-reminder' ) . '</p>',
    ) );
    $screen->add_help_tab( array(
        'id'	=> 'wcrr_help_tab_2',
        'title'	=> __('Unspecified values', 'woocommerce-review-reminder'),
        'content'	=> '<p>' . __( 'As the address and name of the recipient takes all the data, which the buyer indicated in the payment order information.', 'woocommerce-review-reminder' ) . '</p>',
    ) );
	$screen->set_help_sidebar(
		'<p>'. __('Here will be useful links.', 'woocommerce-review-reminder') .'</p>'
	);
}

function pixelix_wc_reminder_callback(){
	require_once('pixelix_wc_reminder_page.php');
}
function pixelix_wcrr_menu(){
	global $pixelix_wc_reminder_callback;
	$pixelix_wc_reminder_callback = add_submenu_page('woocommerce', __('Settings for the reminder', 'woocommerce-review-reminder'), __('Review Reminder', 'woocommerce-review-reminder'), 'manage_options', 'pixelix_wc_reminder', 'pixelix_wc_reminder_callback');
	add_action('load-'.$pixelix_wc_reminder_callback, 'wcrr_admin_add_help_tab');
}

if ( is_admin() ){
  add_action('admin_menu', 'pixelix_wcrr_menu');
  add_action( 'admin_init', 'register_wcrr_settings' );
}

function register_wcrr_settings() {
  register_setting( 'wcrr_options', 'mailer_name' );
  register_setting( 'wcrr_options', 'mailer_email' );
  register_setting( 'wcrr_options', 'interval_count' );
  register_setting( 'wcrr_options', 'interval_type' );
}

function check_wcrr_options(){
	if (get_option('mailer_name') == ''){
		update_option( 'mailer_name', get_bloginfo('name') );
	}
	if (get_option('mailer_email') == ''){
		update_option( 'mailer_email', get_bloginfo('admin_email') );
	}
	if (get_option('interval_count') == '' or get_option('interval_type') == ''){
		update_option( 'interval_count', '1' );
		update_option( 'interval_type', '604800' );
	}
}

function get_permalinks_from_order($order_id){
	global $wpdb;
	$order_item_ids = $wpdb->get_col("
		SELECT
		order_item_id
		FROM
		".$wpdb->prefix."woocommerce_order_items
		WHERE
		order_id = $order_id
	");
	foreach ($order_item_ids as $order_item_id){
		$product_id = $wpdb->get_row("
			SELECT
			meta_value
			FROM
			".$wpdb->prefix."woocommerce_order_itemmeta
			WHERE
			order_item_id = $order_item_id
			AND
			meta_key = '_product_id'
		",
		ARRAY_N);
		$product_ids[] = implode($product_id);
	}
	foreach ($product_ids as $product_id){
		$permalinks =  $permalinks . get_permalink( $product_id ). '<br>';
	}
	return $permalinks;
}

function remind_review($order_id){
	$interval = get_option('interval_type') * get_option('interval_count');
	wp_schedule_single_event( time() + $interval, 'my_new_event', array($order_id));
}
add_action( 'woocommerce_order_status_completed', 'remind_review', 10, 1 );
function set_html_content_type() {
	return 'text/html';
}
function do_this_in_an_hour( $order_id ) {
	check_wcrr_options();
	$to = get_post_meta($order_id, '_billing_email', true);
	$subject = __('Please leave a review', 'woocommerce-review-reminder');
	$message =
		__('Hello', 'woocommerce-review-reminder') .', '. get_post_meta($order_id, '_billing_first_name', true) .'!<br><br>'.
		__('Thank you for choosing our shop', 'woocommerce-review-reminder') .'.<br>'.
		__('We offer you to leave feedback about the purchased product on our website', 'woocommerce-review-reminder') .'.<br>'.
		__('It does not take much time, but perhaps help other customers with a choice of', 'woocommerce-review-reminder') .'.<br>'.
		__('Thanks in advance', 'woocommerce-review-reminder') .'!<br><br>'.
		__('You have bought from us is this', 'woocommerce-review-reminder') .':<br>'.
		get_permalinks_from_order($order_id);
	$headers[] = 'From: '. get_option('mailer_name') .' <'. get_option('mailer_email') .'>';
	add_filter( 'wp_mail_content_type', 'set_html_content_type' );
	wp_mail( $to, $subject, $message, $headers );
	remove_filter( 'wp_mail_content_type', 'set_html_content_type' );
}
add_action( 'my_new_event','do_this_in_an_hour', 1 );

?>
