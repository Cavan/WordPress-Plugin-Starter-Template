<?php
/**
 * Provide a admin area view for the plugin
 *
 * @package    WP_Plugin_Starter
 * @subpackage WP_Plugin_Starter/admin/partials
 */
?>

<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	
	<div class="wp-plugin-starter-admin-content">
		<h2><?php esc_html_e( 'Welcome to the Plugin Starter Template', 'wp-plugin-starter' ); ?></h2>
		<p><?php esc_html_e( 'This is your plugin\'s admin page. Customize it to fit your needs.', 'wp-plugin-starter' ); ?></p>
		
		<form method="post" action="options.php">
			<?php
			settings_fields( 'wp_plugin_starter_settings' );
			do_settings_sections( 'wp_plugin_starter_settings' );
			?>
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Example Setting', 'wp-plugin-starter' ); ?></th>
					<td>
						<input type="text" name="wp_plugin_starter_example" value="<?php echo esc_attr( get_option( 'wp_plugin_starter_example' ) ); ?>" />
					</td>
				</tr>
			</table>
			
			<?php submit_button(); ?>
		</form>
	</div>
</div>
