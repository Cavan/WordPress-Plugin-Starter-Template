<?php
/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and hooks for enqueuing
 * public-facing stylesheet and JavaScript.
 *
 * @package    WP_Plugin_Starter
 * @subpackage WP_Plugin_Starter/public
 */
class WP_Plugin_Starter_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version           The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 */
	public function enqueue_styles() {
		wp_enqueue_style(
			$this->plugin_name,
			WP_PLUGIN_STARTER_URL . 'public/css/public.css',
			array(),
			$this->version,
			'all'
		);
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 */
	public function enqueue_scripts() {
		wp_enqueue_script(
			$this->plugin_name,
			WP_PLUGIN_STARTER_URL . 'public/js/public.js',
			array( 'jquery' ),
			$this->version,
			false
		);
	}
}
