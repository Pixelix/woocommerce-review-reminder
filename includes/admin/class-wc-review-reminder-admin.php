<?php
/**
 * WooCommerce Review Reminder admin.
 */
class WC_Review_Reminder_Admin {

	/**
	 * Instance of this class.
	 *
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @var string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the admin.
	 */
	private function __construct() {
		$this->plugin_slug = WC_Review_Reminder::get_plugin_slug();

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Register settings.
		add_action( 'admin_init', array( $this, 'register_settings' ) );
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
	 * Register the administration menu.
	 *
	 * @return void
	 */
	public function add_plugin_admin_menu() {
		$this->plugin_screen_hook_suffix = add_submenu_page(
			'woocommerce',
			__( 'Settings for the reminder', $this->plugin_slug ),
			__( 'Review Reminder', $this->plugin_slug ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);

		add_action( 'load-' . $this->plugin_screen_hook_suffix, array( $this, 'help_tab' ) );
	}

	/**
	 * Help tabs.
	 *
	 * @return void
	 */
	public function help_tab() {
		$screen = get_current_screen();

		if ( $screen->id != $this->plugin_screen_hook_suffix ) {
			return;
		}

		$screen->add_help_tab(
			array(
				'id'      => 'wcrr_configuring_help_tab',
				'title'   => __( 'Configuring plugin', $this->plugin_slug ),
				'content' => '<p>' . __( 'You can ask your plugin settings. If any of the fields will be empty, the default value is used.', $this->plugin_slug  ) . '</p>',
			)
		);

		$screen->add_help_tab(
			array(
				'id'      => 'wcrr_unspecified_help_tab',
				'title'   => __( 'Unspecified values', $this->plugin_slug ),
				'content' => '<p>' . __( 'As the address and name of the recipient takes all the data, which the buyer indicated in the payment order information.', $this->plugin_slug ) . '</p>',
			)
		);

		// $screen->set_help_sidebar(
			// '<p>'. __( 'Here will be useful links.', $this->plugin_slug ) .'</p>'
		// );
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @return string Settings form.
	 */
	public function display_plugin_admin_page() {
		include_once( 'views/html-admin-settings.php' );
	}


	/**
	 * Register the plugin settings.
	 *
	 * @return void
	 */
	public function register_settings() {
		register_setting( 'wcrr_options', 'mailer_name' );
		register_setting( 'wcrr_options', 'mailer_email' );
		register_setting( 'wcrr_options', 'interval_count' );
		register_setting( 'wcrr_options', 'interval_type' );
	}
}
