---
sidebar_position: 4
---

# Customization Guide

Learn how to customize and adapt the starter template to your specific needs.

## Changing Plugin Name

### Step 1: Rename the Plugin File

Rename `wp-plugin-starter.php` to match your plugin name:

```bash
mv wp-plugin-starter.php your-plugin-name.php
```

### Step 2: Update Plugin Headers

Edit your renamed plugin file and update the headers:

```php
/**
 * Plugin Name:       Your Plugin Name
 * Plugin URI:        https://your-plugin-uri.com
 * Description:       Your plugin description
 * Version:           1.0.0
 * Author:            Your Name
 * Author URI:        https://your-site.com
 * License:           GPL-2.0+
 * Text Domain:       your-plugin-name
 */
```

### Step 3: Find and Replace

Use your IDE or command line to find and replace these strings throughout **all files**:

| Find | Replace | Example |
|------|---------|---------|
| `WP_Plugin_Starter` | `Your_Plugin_Name` | `class WP_Plugin_Starter_Admin` → `class Your_Plugin_Name_Admin` |
| `wp-plugin-starter` | `your-plugin-name` | `'wp-plugin-starter'` → `'your-plugin-name'` |
| `wp_plugin_starter` | `your_plugin_prefix` | `wp_plugin_starter_activate()` → `your_plugin_prefix_activate()` |
| `WP_PLUGIN_STARTER` | `YOUR_PLUGIN_PREFIX` | `WP_PLUGIN_STARTER_VERSION` → `YOUR_PLUGIN_PREFIX_VERSION` |

### Step 4: Rename Files

Rename the class files to match your new naming convention:

```bash
# In includes/
mv class-wp-plugin-starter.php class-your-plugin-name.php

# Update file references in your code
```

## Adding Custom Functionality

### Adding Admin Features

Add new admin functionality to `/admin/class-admin.php`:

```php
class WP_Plugin_Starter_Admin {
    
    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }
    
    // Add your custom admin methods here
    public function my_custom_admin_feature() {
        // Your code here
    }
}
```

Then register it in `includes/class-wp-plugin-starter.php`:

```php
$this->loader->add_action( 'admin_init', $plugin_admin, 'my_custom_admin_feature' );
```

### Adding Public Features

Add new public functionality to `/public/class-public.php`:

```php
class WP_Plugin_Starter_Public {
    
    public function my_custom_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'title' => 'Default',
        ), $atts );
        
        return '<div>' . esc_html( $atts['title'] ) . '</div>';
    }
}
```

Register the shortcode in your constructor:

```php
add_shortcode( 'my_shortcode', array( $this, 'my_custom_shortcode' ) );
```

### Adding New Classes

1. Create a new file in the appropriate directory (e.g., `includes/class-my-feature.php`)
2. Define your class following WordPress coding standards
3. Require the file in the main plugin file or main class
4. Instantiate and use the class

Example:

```php
// includes/class-my-feature.php
class WP_Plugin_Starter_My_Feature {
    
    public function __construct() {
        // Initialize
    }
    
    public function run() {
        // Feature logic
    }
}
```

## Creating Custom Templates

### Admin Templates

Create template files in `/admin/partials/`:

```php
// admin/partials/my-custom-page.php
<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    <!-- Your template content -->
</div>
```

Load the template in your admin class:

```php
public function display_custom_page() {
    require_once plugin_dir_path( __FILE__ ) . 'partials/my-custom-page.php';
}
```

### Public Templates

For public templates, you can:

1. Create a `/templates` directory
2. Add your template files
3. Load them using `load_template()` or `include`

```php
public function load_public_template( $template_name ) {
    $template_path = plugin_dir_path( __FILE__ ) . 'templates/' . $template_name . '.php';
    
    if ( file_exists( $template_path ) ) {
        include $template_path;
    }
}
```

## Adding Settings

### Register Settings

In your admin class:

```php
public function register_settings() {
    register_setting(
        'my_option_group',
        'my_option_name',
        array(
            'sanitize_callback' => array( $this, 'sanitize_setting' ),
        )
    );
}

public function sanitize_setting( $input ) {
    return sanitize_text_field( $input );
}
```

### Add Settings Page

```php
public function add_settings_page() {
    add_options_page(
        'My Plugin Settings',
        'My Plugin',
        'manage_options',
        'my-plugin-settings',
        array( $this, 'display_settings_page' )
    );
}
```

## Custom Post Types and Taxonomies

### Register Custom Post Type

```php
public function register_custom_post_type() {
    $args = array(
        'public' => true,
        'label'  => __( 'My Custom Type', 'your-plugin-name' ),
        'supports' => array( 'title', 'editor', 'thumbnail' ),
        'has_archive' => true,
        'rewrite' => array( 'slug' => 'my-custom-type' ),
    );
    
    register_post_type( 'my_custom_type', $args );
}
```

Register it with the loader:

```php
$this->loader->add_action( 'init', $plugin_admin, 'register_custom_post_type' );
```

## Next Steps

- Review [Code Examples](code-examples) for more implementation patterns
- Check [Resources](resources) for WordPress development documentation
- See [Testing](testing) for quality assurance guidelines
