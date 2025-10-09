# WordPress Plugin Starter Template

A comprehensive, bare-bones starter template for WordPress plugin development. This template provides a solid foundation with proper folder structure, boilerplate code, and extensive code examples following WordPress coding standards.

## Features

- ✅ Standard WordPress plugin folder structure
- ✅ Object-oriented architecture with proper class organization
- ✅ Admin and public-facing functionality separation
- ✅ Asset management (CSS and JavaScript enqueuing)
- ✅ Activation and deactivation hooks
- ✅ Admin menu and settings page
- ✅ Comprehensive code examples for common operations
- ✅ Follows WordPress coding standards
- ✅ **Complete documentation site built with Docusaurus**
- ✅ **Docker support for easy documentation development**

## Quick Start

1. **Clone or download this repository**
2. **Rename the plugin folder** to your desired plugin name
3. **Find and replace** the following strings throughout all files:
   - `WP_Plugin_Starter` → `Your_Plugin_Name` (class names)
   - `wp-plugin-starter` → `your-plugin-name` (slug)
   - `wp_plugin_starter` → `your_plugin_prefix` (function prefix)
   - `WP_PLUGIN_STARTER` → `YOUR_PLUGIN_PREFIX` (constants)
4. **Update the plugin header** in `wp-plugin-starter.php`
5. **Activate the plugin** in WordPress admin

## Folder Structure

```
wp-plugin-starter/
├── admin/                  # Admin-specific functionality
│   ├── css/               # Admin stylesheets
│   ├── js/                # Admin JavaScript
│   ├── partials/          # Admin view templates
│   └── class-admin.php    # Admin class
├── public/                # Public-facing functionality
│   ├── css/               # Public stylesheets
│   ├── js/                # Public JavaScript
│   └── class-public.php   # Public class
├── includes/              # Shared/core functionality
│   ├── class-activator.php    # Activation hooks
│   ├── class-deactivator.php  # Deactivation hooks
│   ├── class-loader.php       # Hook loader
│   └── class-wp-plugin-starter.php  # Main plugin class
├── assets/                # Additional assets
│   └── images/           # Image files
├── languages/             # Translation files (create as needed)
├── docs/                  # Documentation site (Docusaurus)
├── CODE-EXAMPLES.md       # Code examples documentation
├── README.md              # This file
└── wp-plugin-starter.php  # Main plugin file
```

## Documentation

### 📚 Full Documentation Site

For comprehensive documentation, visit the **[Documentation Site](docs/)** built with Docusaurus.

**Run the documentation locally:**

```bash
cd docs
npm install
npm start
```

**Or using Docker:**

```bash
cd docs
docker-compose up
```

Visit `http://localhost:3000` to view the full documentation with:

- **Getting Started Guide** - Quick setup and installation
- **Code Examples** - Practical WordPress code patterns
- **API Reference** - Detailed function and class documentation  
- **Best Practices** - Security, performance, and coding standards
- **Contributing Guide** - How to contribute to the project

### Code Examples

See [CODE-EXAMPLES.md](CODE-EXAMPLES.md) for detailed code examples including:

- **File Operations**: Reading various file types (TXT, CSV, JSON) using WordPress functions
- **Data Looping**: Iterating through posts, custom post types, arrays, and taxonomies
- **Bulk Page Creation**: Creating multiple pages from data files with metadata and templates

### Common Plugin Operations

#### Adding a Custom Post Type

```php
// Add to includes/class-wp-plugin-starter.php or create a new file
function wp_plugin_starter_register_post_type() {
    $args = array(
        'public'    => true,
        'label'     => __( 'Custom Type', 'wp-plugin-starter' ),
        'supports'  => array( 'title', 'editor', 'thumbnail' ),
    );
    register_post_type( 'custom_type', $args );
}
add_action( 'init', 'wp_plugin_starter_register_post_type' );
```

#### Adding Shortcodes

```php
// Add to public/class-public.php
public function register_shortcodes() {
    add_shortcode( 'my_shortcode', array( $this, 'my_shortcode_callback' ) );
}

public function my_shortcode_callback( $atts ) {
    $atts = shortcode_atts( array(
        'title' => 'Default Title',
    ), $atts );
    
    return '<div class="my-shortcode">' . esc_html( $atts['title'] ) . '</div>';
}
```

#### Adding AJAX Handlers

```php
// Add to admin/class-admin.php
public function register_ajax_handlers() {
    add_action( 'wp_ajax_my_action', array( $this, 'handle_ajax_request' ) );
}

public function handle_ajax_request() {
    check_ajax_referer( 'my-nonce', 'nonce' );
    
    $data = array( 'message' => 'Success!' );
    wp_send_json_success( $data );
}
```

## Resources

### Official WordPress Documentation

- [Plugin Handbook](https://developer.wordpress.org/plugins/) - Comprehensive guide to WordPress plugin development
- [Code Reference](https://developer.wordpress.org/reference/) - Complete WordPress function and class reference
- [Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/) - WordPress coding standards and best practices
- [Plugin Security](https://developer.wordpress.org/plugins/security/) - Security best practices for plugins

### WordPress APIs

- [Settings API](https://developer.wordpress.org/plugins/settings/) - Creating plugin settings pages
- [Options API](https://developer.wordpress.org/plugins/settings/options-api/) - Storing and retrieving plugin options
- [Shortcode API](https://developer.wordpress.org/plugins/shortcodes/) - Creating and using shortcodes
- [REST API](https://developer.wordpress.org/rest-api/) - Building REST API endpoints
- [Database API](https://developer.wordpress.org/plugins/database/) - Working with the WordPress database
- [HTTP API](https://developer.wordpress.org/plugins/http-api/) - Making HTTP requests
- [Filesystem API](https://developer.wordpress.org/plugins/filesystem-api/) - Working with files securely

### Development Tools

- [WP-CLI](https://wp-cli.org/) - Command-line interface for WordPress
- [Debug Bar](https://wordpress.org/plugins/debug-bar/) - Debugging plugin for WordPress
- [Query Monitor](https://wordpress.org/plugins/query-monitor/) - Developer tools panel for WordPress
- [Local by Flywheel](https://localwp.com/) - Local WordPress development environment

### Learning Resources

- [WordPress TV](https://wordpress.tv/) - Video tutorials and conference talks
- [WPBeginner](https://www.wpbeginner.com/) - WordPress tutorials for beginners
- [Torque Magazine](https://torquemag.io/) - WordPress news and tutorials
- [WP Engine Resources](https://wpengine.com/resources/) - WordPress development resources

### Plugin Development Best Practices

- [Plugin Security Best Practices](https://developer.wordpress.org/plugins/security/)
  - Always sanitize input data
  - Escape output data
  - Use nonces for form submissions
  - Validate and authenticate users

- [Performance Optimization](https://developer.wordpress.org/plugins/plugin-basics/best-practices/)
  - Load assets only when needed
  - Use transients for caching
  - Optimize database queries
  - Minimize HTTP requests

- [Internationalization (i18n)](https://developer.wordpress.org/plugins/internationalization/)
  - Use translation functions (`__()`, `_e()`, `esc_html__()`)
  - Create `.pot` files for translators
  - Support RTL languages

## Customization Guide

### Changing Plugin Name

1. Rename the main plugin file (`wp-plugin-starter.php`)
2. Update plugin headers in the main file
3. Find and replace all instances of:
   - Class prefix: `WP_Plugin_Starter_` → `Your_Plugin_`
   - Text domain: `wp-plugin-starter` → `your-plugin-name`
   - Function prefix: `wp_plugin_starter_` → `your_plugin_`
   - Constants: `WP_PLUGIN_STARTER_` → `YOUR_PLUGIN_`

### Adding Custom Functionality

1. **Admin features**: Add to `admin/class-admin.php`
2. **Public features**: Add to `public/class-public.php`
3. **Shared features**: Add to `includes/` directory
4. **Register hooks**: Use the loader in `includes/class-wp-plugin-starter.php`

### Creating Custom Templates

Create template files in `admin/partials/` or create a `templates/` directory for public templates.

## Testing

Before deploying your plugin:

1. Test on a fresh WordPress installation
2. Test with different themes
3. Check for PHP errors and warnings
4. Validate HTML output
5. Test with JavaScript console open
6. Test on different PHP versions (7.4+)
7. Use WordPress coding standards checker (PHPCS)

## License

This project is licensed under the GPL v2 or later.

## Contributing

Contributions are welcome! Feel free to submit issues and pull requests.

## Support

For issues and questions:
- Check the [WordPress Plugin Handbook](https://developer.wordpress.org/plugins/)
- Visit [WordPress Stack Exchange](https://wordpress.stackexchange.com/)
- Review the code examples in [CODE-EXAMPLES.md](CODE-EXAMPLES.md)

## Credits

Built following WordPress plugin development best practices and coding standards.