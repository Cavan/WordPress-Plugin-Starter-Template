---
sidebar_position: 2
---

# Quick Start

Get up and running with the WordPress Plugin Starter Template in minutes.

## Installation

1. **Clone or download this repository**

```bash
git clone https://github.com/Cavan/WordPress-Plugin-Starter-Template.git
```

2. **Rename the plugin folder** to your desired plugin name

```bash
mv WordPress-Plugin-Starter-Template your-plugin-name
```

3. **Find and replace** the following strings throughout all files:

| Find | Replace | Usage |
|------|---------|-------|
| `WP_Plugin_Starter` | `Your_Plugin_Name` | Class names |
| `wp-plugin-starter` | `your-plugin-name` | Plugin slug |
| `wp_plugin_starter` | `your_plugin_prefix` | Function prefix |
| `WP_PLUGIN_STARTER` | `YOUR_PLUGIN_PREFIX` | Constants |

4. **Update the plugin header** in `wp-plugin-starter.php`:

```php
/**
 * Plugin Name:       Your Plugin Name
 * Plugin URI:        https://your-plugin-uri.com
 * Description:       Your plugin description
 * Version:           1.0.0
 * Author:            Your Name
 * Author URI:        https://your-site.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       your-plugin-name
 * Domain Path:       /languages
 */
```

5. **Move the plugin** to your WordPress plugins directory:

```bash
cp -r your-plugin-name /path/to/wordpress/wp-content/plugins/
```

6. **Activate the plugin** in WordPress admin (`Plugins` → `Installed Plugins`)

## Verification

After activation, you should see:

1. A new menu item in the WordPress admin sidebar
2. No PHP errors in your error log
3. The plugin listed as active in the Plugins page

## Next Steps

- Review the [Folder Structure](folder-structure) to understand the plugin organization
- Explore [Code Examples](code-examples) for common WordPress operations
- Check out [Customization](customization) to learn how to adapt the template

## Development Environment

For local development, we recommend:

- [Local by Flywheel](https://localwp.com/) - Easy local WordPress setup
- [WP-CLI](https://wp-cli.org/) - Command-line tools for WordPress
- [Query Monitor](https://wordpress.org/plugins/query-monitor/) - Debug plugin for WordPress
