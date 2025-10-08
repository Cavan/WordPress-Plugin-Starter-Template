<?php
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @package    WP_Plugin_Starter
 * @subpackage WP_Plugin_Starter/includes
 */
class WP_Plugin_Starter_Activator {

	/**
	 * Code to run on plugin activation.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		// Flush rewrite rules
		flush_rewrite_rules();

		// Set default options
		if ( ! get_option( 'wp_plugin_starter_settings' ) ) {
			add_option( 'wp_plugin_starter_settings', array() );
		}
	}
}
