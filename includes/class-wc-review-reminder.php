<?php
/**
 * WooCommerce Review Reminder main actions.
 */
class WC_Review_Reminder {

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	const VERSION = '2.0';

	/**
	 * Plugin slug.
	 *
	 * @var string
	 */
	protected static $plugin_slug = 'woocommerce-review-reminder';

	/**
	 * Instance of this class.
	 *
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin actions.
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Schedule the event when the order is completed.
		add_action( 'woocommerce_order_status_completed', array( $this, 'remind_review' ), 10, 1 );

		// Trigger the email.
		add_action( 'woocommerce_review_reminder_new_event', array( $this, 'send_mail' ), 1 );
	}

	/**
	 * Return the plugin slug.
	 *
	 * @return Plugin slug variable.
	 */
	public static function get_plugin_slug() {
		return self::$plugin_slug;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @return object A single instance of this class.
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
	 * @param  boolean $network_wide True if WPMU superadmin uses
	 *                               "Network Activate" action, false if
	 *                               WPMU is disabled or plugin is
	 *                               activated on an individual blog.
	 *
	 * @return void
	 */
	public static function activate( $network_wide ) {
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			if ( $network_wide  ) {
				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {
					switch_to_blog( $blog_id );
					self::single_activate();
				}

				restore_current_blog();
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
	 * @param  boolean $network_wide True if WPMU superadmin uses
	 *                               "Network Deactivate" action, false if
	 *                               WPMU is disabled or plugin is
	 *                               deactivated on an individual blog.
	 *
	 * @return void
	 */
	public static function deactivate( $network_wide ) {
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			if ( $network_wide ) {
				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {
					switch_to_blog( $blog_id );
					self::single_deactivate();
				}

				restore_current_blog();
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
	 * @param  int  $blog_id ID of the new blog.
	 *
	 * @return void
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
	 * @return array|false The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {
		global $wpdb;

		// Get an array of blog ids.
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );
	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @return void
	 */
	private static function single_activate() {
		add_option( 'interval_count', '1' );
		add_option( 'interval_type', '604800' );
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @return void
	 */
	private static function single_deactivate() {
		delete_option( 'mailer_name' ); // Remove for old versions.
		delete_option( 'mailer_email' ); // Remove for old versions.
		delete_option( 'interval_count' );
		delete_option( 'interval_type' );
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {
		$domain = self::get_plugin_slug();
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/lang/' );
	}

	/**
	 * Gets permalinks from order.
	 *
	 * @param  int    $order_id Order ID.
	 *
	 * @return string           Permalinks.
	 */
	protected function get_permalinks_from_order( $order_id ) {
		global $wpdb;
		$permalinks = '<ul>';

		// Get all order items.
		$order_item_ids = $wpdb->get_col(
			$wpdb->prepare( "
				SELECT
				order_item_id
				FROM
				{$wpdb->prefix}woocommerce_order_items
				WHERE
				order_id = %d
			", $order_id )
		);

		// Get products ids.
		foreach ( $order_item_ids as $order_item_id ) {
			$product_id = $wpdb->get_row(
				$wpdb->prepare( "
					SELECT
					meta_value
					FROM
					{$wpdb->prefix}woocommerce_order_itemmeta
					WHERE
					order_item_id = %d
					AND
					meta_key = '_product_id'
				", $order_item_id ),
				ARRAY_N
			);

			// Test whether the product actually was found.
			if ( is_array( $product_id ) ) {
				$product_ids[] = implode( $product_id );
			}
		}

		// Creates products links.
		foreach ( $product_ids as $product_id ) {
			$permalinks .= sprintf( '<li><a href="%1$s" target="_blank">%1$s</a></li>', get_permalink( $product_id ) );
		}

		$permalinks .= '</ul>';

		return $permalinks;
	}

	/**
	 * Remind review action.
	 *
	 * @param  int  $order_id Order ID.
	 *
	 * @return void
	 */
	public function remind_review( $order_id ) {
		$interval_type = get_option( 'interval_type' );
		$interval_type  = ! empty( $interval_type ) ? get_option( 'interval_type' ) : 1;
		$interval_count = get_option( 'interval_count' );
		$interval_count = ! empty( $interval_count ) ? get_option( 'interval_count' ) : 604800;
		$interval       = time() + ( $interval_type * $interval_count );

		wp_schedule_single_event( $interval, 'woocommerce_review_reminder_new_event', array( $order_id ) );
	}

	/**
	 * Sends the email notification.
	 *
	 * @param  int  $order_id Order ID.
	 *
	 * @return void
	 */
	public function send_mail( $order_id ) {
		global $woocommerce;
		$mailer = $woocommerce->mailer();
		$order = new WC_Order( $order_id );

		$subject = apply_filters(
			'woocommerce_review_reminder_email_subject',
			sprintf( __( 'Please leave a review your order %s', self::get_plugin_slug() ), $order->get_order_number() ),
			$order
		);

		// Mail headers.
		$headers = array();
		$headers[] = "Content-Type: text/html\r\n";

		// Message title.
		$message_title = apply_filters(
			'woocommerce_review_reminder_email_title',
			__( 'Please leave a review', self::get_plugin_slug() ),
			$order
		);

		// Message body.
		$body = '<p>' . sprintf( __( 'Hello %s %s!', self::get_plugin_slug() ), $order->billing_first_name, $order->billing_last_name ) . '</p>' .
			'<p>' . __( 'Thank you for choosing our shop.', self::get_plugin_slug() ) . '<br>' .
			__( 'We will be very happy to receive your feedback on purchased products.', self::get_plugin_slug() ) . '<br>' .
			__( 'It does not take much time and can still help the other customers choosing the best product.', self::get_plugin_slug() ) . '<br>' .
			__( 'Thanks in advance!', self::get_plugin_slug() ) . '</p>' .
			'<p>' . __( 'You have bought from us this products:', self::get_plugin_slug() ) . '</p>' .
			$this->get_permalinks_from_order( $order_id );

		$message_body = apply_filters(
			'woocommerce_review_reminder_email_message',
			$body,
			$order,
			$this->get_permalinks_from_order( $order_id )
		);

		// Sets the message template.
		$message = $mailer->wrap_message( $message_title, $message_body );

		// Send the email.
		$mailer->send( $order->billing_email, $subject, $message, $headers, '' );
	}
}
