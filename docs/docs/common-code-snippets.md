---
sidebar_position: 12
---

# Common Code Snippets

This page provides practical code snippets for common WordPress plugin development tasks. Each snippet includes explanations of where to place the code and which functions are being used.

## Uploading Different File Types

### Where This Code Goes

File upload handlers should typically go in:
- **Admin context**: `/admin/class-admin.php` for admin-only uploads
- **Public context**: `/public/class-public.php` for frontend uploads
- **AJAX handlers**: Can be registered in either class depending on context

### Basic File Upload Handler

This example shows how to handle file uploads using WordPress core functions.

**Location**: `/admin/class-admin.php` (as a method in the admin class)

```php
/**
 * Handle file upload
 * 
 * WordPress Functions Used:
 * - current_user_can() - Checks user permissions
 * - wp_handle_upload() - Handles the file upload process
 * - wp_insert_attachment() - Creates attachment post
 * - wp_generate_attachment_metadata() - Generates attachment metadata
 * - wp_update_attachment_metadata() - Updates attachment metadata
 * 
 * PHP Functions Used:
 * - $_FILES superglobal - Accesses uploaded files
 * - is_wp_error() - Checks if return is a WP_Error object
 */
public function handle_file_upload() {
    // Check user permissions
    if ( ! current_user_can( 'upload_files' ) ) {
        wp_die( __( 'You do not have permission to upload files.', 'wp-plugin-starter' ) );
    }
    
    // Verify nonce for security
    check_admin_referer( 'upload_file_action', 'upload_file_nonce' );
    
    // Check if file was uploaded
    if ( empty( $_FILES['my_file'] ) ) {
        wp_die( __( 'No file was uploaded.', 'wp-plugin-starter' ) );
    }
    
    // Required WordPress files for upload functionality
    require_once( ABSPATH . 'wp-admin/includes/file.php' );
    require_once( ABSPATH . 'wp-admin/includes/media.php' );
    require_once( ABSPATH . 'wp-admin/includes/image.php' );
    
    // Handle the upload
    $uploaded_file = wp_handle_upload( 
        $_FILES['my_file'], 
        array( 'test_form' => false ) 
    );
    
    // Check for upload errors
    if ( isset( $uploaded_file['error'] ) ) {
        wp_die( $uploaded_file['error'] );
    }
    
    // Prepare attachment data
    $attachment = array(
        'post_mime_type' => $uploaded_file['type'],
        'post_title'     => sanitize_file_name( $_FILES['my_file']['name'] ),
        'post_content'   => '',
        'post_status'    => 'inherit',
    );
    
    // Insert the attachment into the media library
    $attachment_id = wp_insert_attachment( $attachment, $uploaded_file['file'] );
    
    // Generate and update attachment metadata
    $attachment_data = wp_generate_attachment_metadata( 
        $attachment_id, 
        $uploaded_file['file'] 
    );
    wp_update_attachment_metadata( $attachment_id, $attachment_data );
    
    return $attachment_id;
}
```

### Restricting File Types

**Location**: `/admin/class-admin.php` or `/public/class-public.php`

```php
/**
 * Upload with specific file type restrictions
 * 
 * WordPress Functions Used:
 * - wp_check_filetype() - Validates file type against allowed types
 * - sanitize_file_name() - Sanitizes filename
 * 
 * PHP Functions Used:
 * - in_array() - Checks if value exists in array
 */
public function upload_specific_file_type() {
    // Define allowed file types
    $allowed_types = array( 'jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx' );
    
    if ( empty( $_FILES['my_file'] ) ) {
        return false;
    }
    
    // Check file type
    $file_type = wp_check_filetype( $_FILES['my_file']['name'] );
    
    if ( ! in_array( $file_type['ext'], $allowed_types, true ) ) {
        wp_die( __( 'Invalid file type. Only images, PDFs, and documents are allowed.', 'wp-plugin-starter' ) );
    }
    
    // Proceed with upload using wp_handle_upload()
    require_once( ABSPATH . 'wp-admin/includes/file.php' );
    
    $upload = wp_handle_upload( 
        $_FILES['my_file'], 
        array( 
            'test_form' => false,
            'mimes'     => array(
                'jpg|jpeg|jpe' => 'image/jpeg',
                'png'          => 'image/png',
                'gif'          => 'image/gif',
                'pdf'          => 'application/pdf',
                'doc'          => 'application/msword',
                'docx'         => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ),
        ) 
    );
    
    return $upload;
}
```

### File Upload Form in Admin

**Location**: `/admin/partials/upload-form.php`

```php
<?php
/**
 * Admin file upload form
 * 
 * WordPress Functions Used:
 * - wp_nonce_field() - Creates security nonce field
 * - admin_url() - Gets admin URL
 * - esc_html_e() - Escapes and echoes translated text
 * - submit_button() - Outputs submit button
 */
?>

<div class="wrap">
    <h1><?php esc_html_e( 'Upload File', 'wp-plugin-starter' ); ?></h1>
    
    <form method="post" enctype="multipart/form-data" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
        <?php wp_nonce_field( 'upload_file_action', 'upload_file_nonce' ); ?>
        <input type="hidden" name="action" value="handle_file_upload" />
        
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="my_file">
                        <?php esc_html_e( 'Select File', 'wp-plugin-starter' ); ?>
                    </label>
                </th>
                <td>
                    <input type="file" name="my_file" id="my_file" />
                    <p class="description">
                        <?php esc_html_e( 'Allowed types: JPG, PNG, GIF, PDF, DOC, DOCX', 'wp-plugin-starter' ); ?>
                    </p>
                </td>
            </tr>
        </table>
        
        <?php submit_button( __( 'Upload File', 'wp-plugin-starter' ) ); ?>
    </form>
</div>
```

**Register the form handler**: In `/includes/class-wp-plugin-starter.php`

```php
// Add this in the define_admin_hooks() method
$this->loader->add_action( 'admin_post_handle_file_upload', $plugin_admin, 'handle_file_upload' );
```

## Adding to Admin UI

### Where This Code Goes

Admin UI customizations belong in:
- **Admin pages**: `/admin/class-admin.php` - methods for menu items and page callbacks
- **Admin templates**: `/admin/partials/` - template files for admin pages
- **Hook registration**: `/includes/class-wp-plugin-starter.php` - register hooks via loader

### Adding an Admin Menu Page

**Location**: `/admin/class-admin.php`

```php
/**
 * Add custom admin menu page
 * 
 * WordPress Functions Used:
 * - add_menu_page() - Adds top-level menu page
 * - add_submenu_page() - Adds submenu page
 * - __() - Translates text
 */
public function add_custom_admin_menu() {
    // Add top-level menu
    add_menu_page(
        __( 'My Plugin', 'wp-plugin-starter' ),      // Page title
        __( 'My Plugin', 'wp-plugin-starter' ),      // Menu title
        'manage_options',                             // Capability required
        'my-plugin-main',                             // Menu slug
        array( $this, 'display_main_page' ),         // Callback function
        'dashicons-admin-generic',                    // Icon
        20                                            // Position
    );
    
    // Add submenu page
    add_submenu_page(
        'my-plugin-main',                             // Parent slug
        __( 'Settings', 'wp-plugin-starter' ),       // Page title
        __( 'Settings', 'wp-plugin-starter' ),       // Menu title
        'manage_options',                             // Capability
        'my-plugin-settings',                         // Menu slug
        array( $this, 'display_settings_page' )      // Callback
    );
}

/**
 * Display main admin page
 */
public function display_main_page() {
    require_once plugin_dir_path( __FILE__ ) . 'partials/main-page.php';
}

/**
 * Display settings page
 */
public function display_settings_page() {
    require_once plugin_dir_path( __FILE__ ) . 'partials/settings-page.php';
}
```

**Register the hook**: In `/includes/class-wp-plugin-starter.php`

```php
// In define_admin_hooks() method
$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_custom_admin_menu' );
```

### Adding Settings to Existing Admin Page

**Location**: `/admin/class-admin.php`

```php
/**
 * Register plugin settings
 * 
 * WordPress Functions Used:
 * - register_setting() - Registers a setting and its data
 * - add_settings_section() - Adds a settings section
 * - add_settings_field() - Adds a settings field
 */
public function register_plugin_settings() {
    // Register a setting
    register_setting(
        'my_plugin_settings',                         // Option group
        'my_plugin_option',                           // Option name
        array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '',
        )
    );
    
    // Add settings section
    add_settings_section(
        'my_plugin_main_section',                     // Section ID
        __( 'Main Settings', 'wp-plugin-starter' ),  // Title
        array( $this, 'settings_section_callback' ), // Callback
        'my_plugin_settings'                          // Page slug
    );
    
    // Add settings field
    add_settings_field(
        'my_plugin_field',                            // Field ID
        __( 'Setting Field', 'wp-plugin-starter' ),  // Title
        array( $this, 'settings_field_callback' ),   // Callback
        'my_plugin_settings',                         // Page slug
        'my_plugin_main_section'                      // Section ID
    );
}

/**
 * Settings section callback
 */
public function settings_section_callback() {
    echo '<p>' . esc_html__( 'Configure your plugin settings below.', 'wp-plugin-starter' ) . '</p>';
}

/**
 * Settings field callback
 */
public function settings_field_callback() {
    $value = get_option( 'my_plugin_option', '' );
    echo '<input type="text" name="my_plugin_option" value="' . esc_attr( $value ) . '" class="regular-text" />';
}
```

**Register the hook**: In `/includes/class-wp-plugin-starter.php`

```php
// In define_admin_hooks() method
$this->loader->add_action( 'admin_init', $plugin_admin, 'register_plugin_settings' );
```

### Adding Custom Admin Columns

**Location**: `/admin/class-admin.php`

```php
/**
 * Add custom columns to posts list
 * 
 * WordPress Functions Used:
 * - apply_filters() - Used internally by WordPress for column filters
 * - get_post_meta() - Retrieves post metadata
 * - esc_html() - Escapes HTML
 * 
 * PHP Functions Used:
 * - array_slice() - Extracts portion of array
 */
public function add_custom_columns( $columns ) {
    // Insert custom column after title
    $new_columns = array_slice( $columns, 0, 2, true ) +
                   array( 'custom_field' => __( 'Custom Field', 'wp-plugin-starter' ) ) +
                   array_slice( $columns, 2, null, true );
    
    return $new_columns;
}

/**
 * Display custom column content
 */
public function display_custom_column( $column, $post_id ) {
    if ( 'custom_field' === $column ) {
        $value = get_post_meta( $post_id, 'custom_field_key', true );
        echo esc_html( $value ? $value : __( 'N/A', 'wp-plugin-starter' ) );
    }
}

/**
 * Make custom column sortable
 */
public function make_column_sortable( $columns ) {
    $columns['custom_field'] = 'custom_field';
    return $columns;
}
```

**Register the hooks**: In `/includes/class-wp-plugin-starter.php`

```php
// In define_admin_hooks() method
$this->loader->add_filter( 'manage_post_posts_columns', $plugin_admin, 'add_custom_columns' );
$this->loader->add_action( 'manage_post_posts_custom_column', $plugin_admin, 'display_custom_column', 10, 2 );
$this->loader->add_filter( 'manage_edit-post_sortable_columns', $plugin_admin, 'make_column_sortable' );
```

### Adding Meta Boxes

**Location**: `/admin/class-admin.php`

```php
/**
 * Add custom meta box
 * 
 * WordPress Functions Used:
 * - add_meta_box() - Adds a meta box to one or more screens
 */
public function add_custom_meta_box() {
    add_meta_box(
        'my_meta_box',                                    // Meta box ID
        __( 'My Meta Box', 'wp-plugin-starter' ),        // Title
        array( $this, 'render_meta_box' ),               // Callback
        'post',                                           // Screen (post type)
        'normal',                                         // Context (normal, side, advanced)
        'default'                                         // Priority (high, default, low)
    );
}

/**
 * Render meta box content
 * 
 * WordPress Functions Used:
 * - wp_nonce_field() - Creates nonce field
 * - get_post_meta() - Gets post metadata
 * - esc_attr() - Escapes attribute
 */
public function render_meta_box( $post ) {
    // Add nonce for security
    wp_nonce_field( 'my_meta_box_nonce', 'my_meta_box_nonce_field' );
    
    // Get current value
    $value = get_post_meta( $post->ID, 'my_meta_key', true );
    
    // Output field
    ?>
    <label for="my_meta_field">
        <?php esc_html_e( 'Custom Field:', 'wp-plugin-starter' ); ?>
    </label>
    <input type="text" id="my_meta_field" name="my_meta_field" value="<?php echo esc_attr( $value ); ?>" class="widefat" />
    <?php
}

/**
 * Save meta box data
 * 
 * WordPress Functions Used:
 * - wp_verify_nonce() - Verifies nonce
 * - current_user_can() - Checks user capabilities
 * - sanitize_text_field() - Sanitizes text input
 * - update_post_meta() - Updates post metadata
 */
public function save_meta_box( $post_id ) {
    // Verify nonce
    if ( ! isset( $_POST['my_meta_box_nonce_field'] ) || 
         ! wp_verify_nonce( $_POST['my_meta_box_nonce_field'], 'my_meta_box_nonce' ) ) {
        return;
    }
    
    // Check autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    
    // Check permissions
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
    
    // Save data
    if ( isset( $_POST['my_meta_field'] ) ) {
        $value = sanitize_text_field( $_POST['my_meta_field'] );
        update_post_meta( $post_id, 'my_meta_key', $value );
    }
}
```

**Register the hooks**: In `/includes/class-wp-plugin-starter.php`

```php
// In define_admin_hooks() method
$this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'add_custom_meta_box' );
$this->loader->add_action( 'save_post', $plugin_admin, 'save_meta_box' );
```

## Adding to Public UI

### Where This Code Goes

Public-facing features belong in:
- **Public functionality**: `/public/class-public.php` - methods for public features
- **Hook registration**: `/includes/class-wp-plugin-starter.php` - register hooks via loader
- **Templates**: Create a `/templates` directory in plugin root for template files

### Adding Content to Posts

**Location**: `/public/class-public.php`

```php
/**
 * Add custom content before post content
 * 
 * WordPress Functions Used:
 * - is_single() - Checks if viewing a single post
 * - get_post_meta() - Gets post metadata
 * - esc_html() - Escapes HTML
 * 
 * PHP Functions Used:
 * - Concatenation operator - Joins strings
 */
public function add_content_before_post( $content ) {
    // Only on single posts
    if ( ! is_single() ) {
        return $content;
    }
    
    global $post;
    
    // Get custom field value
    $custom_value = get_post_meta( $post->ID, 'my_meta_key', true );
    
    if ( $custom_value ) {
        $custom_content = '<div class="my-custom-content">';
        $custom_content .= '<p><strong>' . esc_html__( 'Custom Info:', 'wp-plugin-starter' ) . '</strong> ';
        $custom_content .= esc_html( $custom_value ) . '</p>';
        $custom_content .= '</div>';
        
        $content = $custom_content . $content;
    }
    
    return $content;
}
```

**Register the hook**: In `/includes/class-wp-plugin-starter.php`

```php
// In define_public_hooks() method
$this->loader->add_filter( 'the_content', $plugin_public, 'add_content_before_post' );
```

### Creating Shortcodes

**Location**: `/public/class-public.php`

```php
/**
 * Register shortcodes in constructor
 */
public function __construct( $plugin_name, $version ) {
    $this->plugin_name = $plugin_name;
    $this->version = $version;
    
    // Register shortcodes
    add_shortcode( 'my_shortcode', array( $this, 'my_shortcode_callback' ) );
}

/**
 * Shortcode callback
 * 
 * WordPress Functions Used:
 * - shortcode_atts() - Merges user shortcode attributes with defaults
 * - esc_html() - Escapes HTML
 * 
 * @param array $atts Shortcode attributes
 * @return string Shortcode output
 */
public function my_shortcode_callback( $atts ) {
    // Parse attributes with defaults
    $atts = shortcode_atts( array(
        'title'   => 'Default Title',
        'content' => 'Default content',
        'color'   => 'blue',
    ), $atts, 'my_shortcode' );
    
    // Build output
    $output = '<div class="my-shortcode" style="color: ' . esc_attr( $atts['color'] ) . ';">';
    $output .= '<h3>' . esc_html( $atts['title'] ) . '</h3>';
    $output .= '<p>' . esc_html( $atts['content'] ) . '</p>';
    $output .= '</div>';
    
    return $output;
}
```

**Usage**: `[my_shortcode title="Hello" content="World" color="red"]`

### Adding Widgets

**Location**: `/public/class-widget.php` (create this new file)

```php
<?php
/**
 * Custom widget class
 * 
 * WordPress Functions Used:
 * - WP_Widget class - Base class for widgets
 * - esc_attr() - Escapes attributes
 * - esc_html() - Escapes HTML
 */
class My_Custom_Widget extends WP_Widget {
    
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(
            'my_custom_widget',                           // Widget ID
            __( 'My Custom Widget', 'wp-plugin-starter' ), // Widget name
            array( 
                'description' => __( 'A custom widget.', 'wp-plugin-starter' ),
            )
        );
    }
    
    /**
     * Front-end display of widget
     * 
     * @param array $args Widget arguments
     * @param array $instance Saved values from database
     */
    public function widget( $args, $instance ) {
        echo $args['before_widget'];
        
        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'];
            echo esc_html( $instance['title'] );
            echo $args['after_title'];
        }
        
        if ( ! empty( $instance['content'] ) ) {
            echo '<p>' . esc_html( $instance['content'] ) . '</p>';
        }
        
        echo $args['after_widget'];
    }
    
    /**
     * Back-end widget form
     * 
     * @param array $instance Previously saved values from database
     */
    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : '';
        $content = ! empty( $instance['content'] ) ? $instance['content'] : '';
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
                <?php esc_html_e( 'Title:', 'wp-plugin-starter' ); ?>
            </label>
            <input class="widefat" 
                   id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" 
                   name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" 
                   type="text" 
                   value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'content' ) ); ?>">
                <?php esc_html_e( 'Content:', 'wp-plugin-starter' ); ?>
            </label>
            <textarea class="widefat" 
                      id="<?php echo esc_attr( $this->get_field_id( 'content' ) ); ?>" 
                      name="<?php echo esc_attr( $this->get_field_name( 'content' ) ); ?>"><?php echo esc_textarea( $content ); ?></textarea>
        </p>
        <?php
    }
    
    /**
     * Sanitize widget form values
     * 
     * WordPress Functions Used:
     * - sanitize_text_field() - Sanitizes text input
     * 
     * @param array $new_instance New settings
     * @param array $old_instance Previous settings
     * @return array Updated settings
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = sanitize_text_field( $new_instance['title'] );
        $instance['content'] = sanitize_text_field( $new_instance['content'] );
        return $instance;
    }
}

/**
 * Register widget
 * 
 * WordPress Functions Used:
 * - register_widget() - Registers a widget
 */
function register_my_custom_widget() {
    register_widget( 'My_Custom_Widget' );
}
add_action( 'widgets_init', 'register_my_custom_widget' );
```

**Include the widget file**: In `/includes/class-wp-plugin-starter.php` in the `load_dependencies()` method:

```php
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-widget.php';
```

### Modifying Template Output

**Location**: `/public/class-public.php`

```php
/**
 * Modify excerpt length
 * 
 * WordPress Functions Used:
 * - apply_filters() - Applied by WordPress to excerpt_length
 * 
 * @param int $length Default excerpt length
 * @return int Modified excerpt length
 */
public function custom_excerpt_length( $length ) {
    return 30; // Return custom length
}

/**
 * Modify excerpt more string
 * 
 * WordPress Functions Used:
 * - get_permalink() - Gets post permalink
 * 
 * @param string $more Default more string
 * @return string Modified more string
 */
public function custom_excerpt_more( $more ) {
    return '... <a href="' . get_permalink() . '">' . __( 'Read More', 'wp-plugin-starter' ) . '</a>';
}
```

**Register the hooks**: In `/includes/class-wp-plugin-starter.php`

```php
// In define_public_hooks() method
$this->loader->add_filter( 'excerpt_length', $plugin_public, 'custom_excerpt_length', 999 );
$this->loader->add_filter( 'excerpt_more', $plugin_public, 'custom_excerpt_more' );
```

## Best Practices: Mixing HTML and PHP

### Using Template Files (Recommended)

**Best Practice**: Separate logic from presentation by using template files.

**Location**: `/admin/partials/my-template.php` or `/public/templates/my-template.php`

```php
<?php
/**
 * Template file for displaying content
 * 
 * This file should contain mostly HTML with minimal PHP
 * All data should be prepared before including this template
 * 
 * WordPress Functions Used:
 * - esc_html() - Escapes HTML text
 * - esc_attr() - Escapes HTML attributes
 * - esc_url() - Escapes URLs
 * - esc_html__() / esc_html_e() - Translates and escapes
 */

// Get data passed to template (set before including)
$title = isset( $title ) ? $title : '';
$items = isset( $items ) ? $items : array();
$show_footer = isset( $show_footer ) ? $show_footer : true;
?>

<div class="my-plugin-wrapper">
    <header class="my-plugin-header">
        <h2><?php echo esc_html( $title ); ?></h2>
    </header>
    
    <div class="my-plugin-content">
        <?php if ( ! empty( $items ) ) : ?>
            <ul class="my-plugin-list">
                <?php foreach ( $items as $item ) : ?>
                    <li class="list-item">
                        <a href="<?php echo esc_url( $item['url'] ); ?>">
                            <?php echo esc_html( $item['name'] ); ?>
                        </a>
                        <?php if ( ! empty( $item['description'] ) ) : ?>
                            <span class="description">
                                <?php echo esc_html( $item['description'] ); ?>
                            </span>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else : ?>
            <p class="no-items">
                <?php esc_html_e( 'No items found.', 'wp-plugin-starter' ); ?>
            </p>
        <?php endif; ?>
    </div>
    
    <?php if ( $show_footer ) : ?>
        <footer class="my-plugin-footer">
            <p><?php esc_html_e( 'Footer content here.', 'wp-plugin-starter' ); ?></p>
        </footer>
    <?php endif; ?>
</div>
```

**Loading the template**: In your class method

```php
/**
 * Display template with data
 * 
 * WordPress Functions Used:
 * - plugin_dir_path() - Gets plugin directory path
 * 
 * PHP Functions Used:
 * - file_exists() - Checks if file exists
 * - include - Includes PHP file
 */
public function display_my_template() {
    // Prepare data
    $title = 'My Custom Title';
    $items = array(
        array(
            'name'        => 'Item 1',
            'url'         => 'https://example.com/item1',
            'description' => 'First item',
        ),
        array(
            'name'        => 'Item 2',
            'url'         => 'https://example.com/item2',
            'description' => 'Second item',
        ),
    );
    $show_footer = true;
    
    // Load template
    $template_path = plugin_dir_path( __FILE__ ) . 'partials/my-template.php';
    
    if ( file_exists( $template_path ) ) {
        include $template_path;
    }
}
```

### Using Output Buffering

**Use Case**: When you need to return HTML instead of echoing it (e.g., shortcodes)

**Location**: `/public/class-public.php`

```php
/**
 * Shortcode with complex HTML using output buffering
 * 
 * WordPress Functions Used:
 * - shortcode_atts() - Merges attributes
 * - esc_html() - Escapes HTML
 * 
 * PHP Functions Used:
 * - ob_start() - Starts output buffering
 * - ob_get_clean() - Gets buffer contents and cleans buffer
 */
public function complex_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'posts_per_page' => 5,
        'category'       => '',
    ), $atts );
    
    // Start output buffering
    ob_start();
    
    // Query posts
    $query = new WP_Query( array(
        'posts_per_page' => intval( $atts['posts_per_page'] ),
        'category_name'  => sanitize_text_field( $atts['category'] ),
    ) );
    
    ?>
    <div class="shortcode-wrapper">
        <?php if ( $query->have_posts() ) : ?>
            <ul class="posts-list">
                <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                    <li>
                        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                        <div class="excerpt"><?php the_excerpt(); ?></div>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else : ?>
            <p><?php esc_html_e( 'No posts found.', 'wp-plugin-starter' ); ?></p>
        <?php endif; ?>
    </div>
    <?php
    
    wp_reset_postdata();
    
    // Return buffered content
    return ob_get_clean();
}
```

### Alternative Syntax for Control Structures

**Best Practice**: Use alternative syntax for better readability in templates

```php
<?php
/**
 * Alternative syntax examples
 * More readable when mixing PHP and HTML
 */
?>

<!-- IF Statement -->
<?php if ( $condition ) : ?>
    <p>Condition is true</p>
<?php else : ?>
    <p>Condition is false</p>
<?php endif; ?>

<!-- FOREACH Loop -->
<?php foreach ( $items as $item ) : ?>
    <div class="item">
        <?php echo esc_html( $item ); ?>
    </div>
<?php endforeach; ?>

<!-- FOR Loop -->
<?php for ( $i = 0; $i < 10; $i++ ) : ?>
    <p>Number: <?php echo intval( $i ); ?></p>
<?php endfor; ?>

<!-- WHILE Loop -->
<?php while ( have_posts() ) : the_post(); ?>
    <article>
        <h2><?php the_title(); ?></h2>
        <div><?php the_content(); ?></div>
    </article>
<?php endwhile; ?>
```

### Proper Escaping

**Critical**: Always escape output based on context

```php
<?php
/**
 * Escaping examples for different contexts
 * 
 * WordPress Functions Used:
 * - esc_html() - For HTML content
 * - esc_attr() - For HTML attributes
 * - esc_url() - For URLs
 * - esc_js() - For JavaScript strings
 * - esc_textarea() - For textarea content
 * - wp_kses_post() - For post content (allows safe HTML)
 */
?>

<!-- HTML Content -->
<p><?php echo esc_html( $user_input ); ?></p>

<!-- HTML Attributes -->
<div class="<?php echo esc_attr( $class_name ); ?>" data-id="<?php echo esc_attr( $id ); ?>">
    Content
</div>

<!-- URLs -->
<a href="<?php echo esc_url( $link ); ?>">Link Text</a>

<!-- Textarea -->
<textarea><?php echo esc_textarea( $textarea_content ); ?></textarea>

<!-- Allow Safe HTML (post content) -->
<div class="user-content">
    <?php echo wp_kses_post( $post_content ); ?>
</div>

<!-- JavaScript -->
<script>
var userMessage = '<?php echo esc_js( $message ); ?>';
</script>

<!-- Translation with escaping -->
<h1><?php esc_html_e( 'Welcome', 'wp-plugin-starter' ); ?></h1>
<p><?php echo esc_html__( 'Description text', 'wp-plugin-starter' ); ?></p>
```

### Inline PHP Statements

**Best Practice**: Keep inline PHP short and simple

```php
<!-- Good: Short, simple inline PHP -->
<div class="<?php echo esc_attr( $class ); ?>">
    <?php echo esc_html( $title ); ?>
</div>

<!-- Avoid: Complex logic inline -->
<!-- Instead, prepare the data before the template -->
<?php
// Prepare data BEFORE template
$display_class = $is_active ? 'active' : 'inactive';
$formatted_date = date_i18n( get_option( 'date_format' ), strtotime( $post_date ) );
?>

<!-- Then use in template -->
<div class="post <?php echo esc_attr( $display_class ); ?>">
    <span class="date"><?php echo esc_html( $formatted_date ); ?></span>
</div>
```

### Template Organization

**Best Practice**: Organize templates by feature/context

```
/admin/
  /partials/
    admin-display.php        # Main admin page
    settings-page.php        # Settings page
    meta-box-content.php     # Meta box template
    upload-form.php          # Upload form

/public/
  /templates/
    shortcode-display.php    # Shortcode template
    widget-display.php       # Widget template
    content-block.php        # Reusable content block
```

**Loading nested templates**:

```php
/**
 * Load a partial template from within another template
 * 
 * PHP Functions Used:
 * - file_exists() - Checks file exists
 * - include - Includes file
 */
public function load_partial( $partial_name, $data = array() ) {
    $template_path = plugin_dir_path( __FILE__ ) . 'partials/' . $partial_name . '.php';
    
    if ( file_exists( $template_path ) ) {
        // Extract data array to variables
        extract( $data );
        include $template_path;
    }
}
```

**Usage in main template**:

```php
<div class="main-content">
    <?php 
    $this->load_partial( 'header', array( 'title' => $page_title ) );
    $this->load_partial( 'content', array( 'items' => $items ) );
    $this->load_partial( 'footer' );
    ?>
</div>
```

## Summary

### Key Takeaways

1. **File Uploads**: Use `wp_handle_upload()` and related WordPress functions, place handlers in appropriate class contexts
2. **Admin UI**: Use `add_menu_page()`, `add_meta_box()`, and register hooks via the loader in `/includes/class-wp-plugin-starter.php`
3. **Public UI**: Use shortcodes, content filters, and widgets, register in `/public/class-public.php`
4. **HTML/PHP Mixing**: Use template files, output buffering, alternative syntax, and proper escaping
5. **Security**: Always sanitize input, escape output, verify nonces, and check user capabilities

### Where Code Goes

- **Admin functionality**: `/admin/class-admin.php`
- **Admin templates**: `/admin/partials/`
- **Public functionality**: `/public/class-public.php`
- **Public templates**: `/public/templates/` or `/templates/`
- **Hook registration**: `/includes/class-wp-plugin-starter.php`
- **Core classes**: `/includes/`

### WordPress Functions Reference

- [WordPress Code Reference](https://developer.wordpress.org/reference/)
- [Plugin Handbook](https://developer.wordpress.org/plugins/)
- [Codex: Function Reference](https://codex.wordpress.org/Function_Reference)

## Related Documentation

- [Folder Structure](folder-structure) - Understanding plugin organization
- [Customization Guide](customization) - Extending the template
- [AJAX Handlers](ajax-handlers) - AJAX file uploads and requests
- [Shortcodes](shortcodes) - More shortcode examples
- [Resources](resources) - Security and best practices
