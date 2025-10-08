<?php
/**
 * Plugin Name: WordPress Plugin Starter Template
 * Plugin URI: https://github.com/Cavan/WordPress-Plugin-Starter-Template
 * Description: A bare-bones starter template for WordPress plugin development with proper structure and boilerplate code.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://yourwebsite.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: wp-plugin-starter
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Current plugin version.
 */
define( 'WP_PLUGIN_STARTER_VERSION', '1.0.0' );

/**
 * Define plugin constants
 */
define( 'WP_PLUGIN_STARTER_PATH', plugin_dir_path( __FILE__ ) );
define( 'WP_PLUGIN_STARTER_URL', plugin_dir_url( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 */
function activate_wp_plugin_starter() {
	require_once WP_PLUGIN_STARTER_PATH . 'includes/class-activator.php';
	WP_Plugin_Starter_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_wp_plugin_starter() {
	require_once WP_PLUGIN_STARTER_PATH . 'includes/class-deactivator.php';
	WP_Plugin_Starter_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wp_plugin_starter' );
register_deactivation_hook( __FILE__, 'deactivate_wp_plugin_starter' );

/**
 * The core plugin class
 */
require WP_PLUGIN_STARTER_PATH . 'includes/class-wp-plugin-starter.php';

/**
 * Begins execution of the plugin.
 */
function run_wp_plugin_starter() {
	$plugin = new WP_Plugin_Starter();
	$plugin->run();
}
run_wp_plugin_starter();
