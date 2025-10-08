<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @package    WP_Plugin_Starter
 * @subpackage WP_Plugin_Starter/includes
 */
class WP_Plugin_Starter {

	/**
	 * The loader that's responsible for maintaining and registering all hooks.
	 *
	 * @var      WP_Plugin_Starter_Loader    $loader
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @var      string    $plugin_name
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @var      string    $version
	 */
	protected $version;

	/**
	 * Initialize the plugin and set its properties.
	 */
	public function __construct() {
		$this->version     = defined( 'WP_PLUGIN_STARTER_VERSION' ) ? WP_PLUGIN_STARTER_VERSION : '1.0.0';
		$this->plugin_name = 'wp-plugin-starter';

		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 */
	private function load_dependencies() {
		require_once WP_PLUGIN_STARTER_PATH . 'includes/class-loader.php';
		require_once WP_PLUGIN_STARTER_PATH . 'admin/class-admin.php';
		require_once WP_PLUGIN_STARTER_PATH . 'public/class-public.php';

		$this->loader = new WP_Plugin_Starter_Loader();
	}

	/**
	 * Register all hooks related to the admin area functionality.
	 */
	private function define_admin_hooks() {
		$plugin_admin = new WP_Plugin_Starter_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_admin_menu' );
	}

	/**
	 * Register all hooks related to the public-facing functionality.
	 */
	private function define_public_hooks() {
		$plugin_public = new WP_Plugin_Starter_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it.
	 *
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks.
	 *
	 * @return    WP_Plugin_Starter_Loader    Orchestrates the hooks.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return    string    The version number.
	 */
	public function get_version() {
		return $this->version;
	}
}
