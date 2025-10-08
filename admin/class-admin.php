<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and hooks for enqueuing
 * admin-specific stylesheet and JavaScript.
 *
 * @package    WP_Plugin_Starter
 * @subpackage WP_Plugin_Starter/admin
 */
class WP_Plugin_Starter_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @var      string    $plugin_name
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @var      string    $version
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version           The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 */
	public function enqueue_styles() {
		wp_enqueue_style(
			$this->plugin_name,
			WP_PLUGIN_STARTER_URL . 'admin/css/admin.css',
			array(),
			$this->version,
			'all'
		);
	}

	/**
	 * Register the JavaScript for the admin area.
	 */
	public function enqueue_scripts() {
		wp_enqueue_script(
			$this->plugin_name,
			WP_PLUGIN_STARTER_URL . 'admin/js/admin.js',
			array( 'jquery' ),
			$this->version,
			false
		);
	}

	/**
	 * Add admin menu page.
	 */
	public function add_admin_menu() {
		add_menu_page(
			__( 'Plugin Starter', 'wp-plugin-starter' ),
			__( 'Plugin Starter', 'wp-plugin-starter' ),
			'manage_options',
			'wp-plugin-starter',
			array( $this, 'display_admin_page' ),
			'dashicons-admin-generic',
			100
		);
	}

	/**
	 * Display the admin page.
	 */
	public function display_admin_page() {
		require_once WP_PLUGIN_STARTER_PATH . 'admin/partials/admin-display.php';
	}
}
