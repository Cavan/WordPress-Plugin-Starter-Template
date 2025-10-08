<?php
/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @package    WP_Plugin_Starter
 * @subpackage WP_Plugin_Starter/includes
 */
class WP_Plugin_Starter_Deactivator {

	/**
	 * Code to run on plugin deactivation.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		// Flush rewrite rules
		flush_rewrite_rules();
	}
}
