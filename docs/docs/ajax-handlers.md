---
sidebar_position: 11
---

# AJAX Handlers

Learn how to implement AJAX functionality in your WordPress plugin.

## Basic AJAX Handler

Set up a basic AJAX handler:

```php
/**
 * Register AJAX handlers
 */
function wp_plugin_starter_register_ajax() {
    add_action( 'wp_ajax_my_action', 'wp_plugin_starter_ajax_handler' );
    add_action( 'wp_ajax_nopriv_my_action', 'wp_plugin_starter_ajax_handler' );
}
add_action( 'init', 'wp_plugin_starter_register_ajax' );

/**
 * AJAX handler callback
 */
function wp_plugin_starter_ajax_handler() {
    check_ajax_referer( 'my_nonce', 'nonce' );
    
    $data = array(
        'message' => 'Success!',
        'time'    => current_time( 'mysql' ),
    );
    
    wp_send_json_success( $data );
}
```

- `wp_ajax_*` - For logged-in users
- `wp_ajax_nopriv_*` - For non-logged-in users

## Frontend JavaScript

Enqueue and localize script for AJAX:

```php
/**
 * Enqueue AJAX scripts
 */
function wp_plugin_starter_enqueue_ajax_script() {
    wp_enqueue_script( 
        'ajax-handler', 
        plugin_dir_url( __FILE__ ) . 'js/ajax-handler.js', 
        array( 'jquery' ), 
        '1.0', 
        true 
    );
    
    wp_localize_script( 'ajax-handler', 'wpPluginAjax', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'my_nonce' ),
    ) );
}
add_action( 'wp_enqueue_scripts', 'wp_plugin_starter_enqueue_ajax_script' );
```

**JavaScript file** (`js/ajax-handler.js`):
```javascript
jQuery(document).ready(function($) {
    $('#my-button').on('click', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: wpPluginAjax.ajax_url,
            type: 'POST',
            data: {
                action: 'my_action',
                nonce: wpPluginAjax.nonce
            },
            success: function(response) {
                if (response.success) {
                    console.log(response.data.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
            }
        });
    });
});
```

## AJAX with Form Data

Handle form submissions via AJAX:

```php
/**
 * Handle form submission
 */
function wp_plugin_starter_process_form() {
    check_ajax_referer( 'form_nonce', 'nonce' );
    
    // Verify user capability
    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_send_json_error( array(
            'message' => __( 'Insufficient permissions', 'wp-plugin-starter' ),
        ) );
    }
    
    // Get and sanitize form data
    $name = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '';
    $email = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
    
    // Validate data
    if ( empty( $name ) || empty( $email ) ) {
        wp_send_json_error( array(
            'message' => __( 'All fields are required', 'wp-plugin-starter' ),
        ) );
    }
    
    if ( ! is_email( $email ) ) {
        wp_send_json_error( array(
            'message' => __( 'Invalid email address', 'wp-plugin-starter' ),
        ) );
    }
    
    // Process the form
    // ... your logic here ...
    
    wp_send_json_success( array(
        'message' => __( 'Form submitted successfully!', 'wp-plugin-starter' ),
        'data'    => array(
            'name'  => $name,
            'email' => $email,
        ),
    ) );
}
add_action( 'wp_ajax_process_form', 'wp_plugin_starter_process_form' );
```

**JavaScript:**
```javascript
$('#my-form').on('submit', function(e) {
    e.preventDefault();
    
    var formData = {
        action: 'process_form',
        nonce: wpPluginAjax.nonce,
        name: $('#name').val(),
        email: $('#email').val()
    };
    
    $.post(wpPluginAjax.ajax_url, formData, function(response) {
        if (response.success) {
            alert(response.data.message);
        } else {
            alert('Error: ' + response.data.message);
        }
    });
});
```

## Load Posts via AJAX

Dynamically load posts:

```php
/**
 * Load posts via AJAX
 */
function wp_plugin_starter_load_posts() {
    check_ajax_referer( 'load_posts_nonce', 'nonce' );
    
    $paged = isset( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;
    $category = isset( $_POST['category'] ) ? sanitize_text_field( $_POST['category'] ) : '';
    
    $args = array(
        'post_type'      => 'post',
        'posts_per_page' => 5,
        'paged'          => $paged,
    );
    
    if ( ! empty( $category ) ) {
        $args['category_name'] = $category;
    }
    
    $query = new WP_Query( $args );
    
    if ( ! $query->have_posts() ) {
        wp_send_json_error( array(
            'message' => __( 'No posts found', 'wp-plugin-starter' ),
        ) );
    }
    
    $posts = array();
    
    while ( $query->have_posts() ) {
        $query->the_post();
        
        $posts[] = array(
            'id'        => get_the_ID(),
            'title'     => get_the_title(),
            'excerpt'   => get_the_excerpt(),
            'permalink' => get_permalink(),
            'thumbnail' => get_the_post_thumbnail_url( get_the_ID(), 'medium' ),
        );
    }
    
    wp_reset_postdata();
    
    wp_send_json_success( array(
        'posts'      => $posts,
        'has_more'   => $paged < $query->max_num_pages,
        'max_pages'  => $query->max_num_pages,
    ) );
}
add_action( 'wp_ajax_load_posts', 'wp_plugin_starter_load_posts' );
add_action( 'wp_ajax_nopriv_load_posts', 'wp_plugin_starter_load_posts' );
```

**JavaScript:**
```javascript
var currentPage = 1;

function loadPosts(category) {
    $.ajax({
        url: wpPluginAjax.ajax_url,
        type: 'POST',
        data: {
            action: 'load_posts',
            nonce: wpPluginAjax.nonce,
            page: currentPage,
            category: category
        },
        beforeSend: function() {
            $('#loading').show();
        },
        success: function(response) {
            if (response.success) {
                var posts = response.data.posts;
                var html = '';
                
                posts.forEach(function(post) {
                    html += '<article>';
                    html += '<h3><a href="' + post.permalink + '">' + post.title + '</a></h3>';
                    html += '<p>' + post.excerpt + '</p>';
                    html += '</article>';
                });
                
                $('#posts-container').append(html);
                
                if (!response.data.has_more) {
                    $('#load-more').hide();
                }
                
                currentPage++;
            }
        },
        complete: function() {
            $('#loading').hide();
        }
    });
}

$('#load-more').on('click', function(e) {
    e.preventDefault();
    loadPosts($('#category-filter').val());
});
```

## File Upload via AJAX

Handle file uploads:

```php
/**
 * Handle file upload
 */
function wp_plugin_starter_upload_file() {
    check_ajax_referer( 'upload_nonce', 'nonce' );
    
    if ( ! current_user_can( 'upload_files' ) ) {
        wp_send_json_error( array(
            'message' => __( 'You do not have permission to upload files', 'wp-plugin-starter' ),
        ) );
    }
    
    if ( empty( $_FILES['file'] ) ) {
        wp_send_json_error( array(
            'message' => __( 'No file uploaded', 'wp-plugin-starter' ),
        ) );
    }
    
    require_once( ABSPATH . 'wp-admin/includes/file.php' );
    require_once( ABSPATH . 'wp-admin/includes/media.php' );
    require_once( ABSPATH . 'wp-admin/includes/image.php' );
    
    $file = $_FILES['file'];
    
    // Handle the upload
    $upload = wp_handle_upload( $file, array( 'test_form' => false ) );
    
    if ( isset( $upload['error'] ) ) {
        wp_send_json_error( array(
            'message' => $upload['error'],
        ) );
    }
    
    // Create attachment
    $attachment = array(
        'post_mime_type' => $upload['type'],
        'post_title'     => sanitize_file_name( $file['name'] ),
        'post_content'   => '',
        'post_status'    => 'inherit',
    );
    
    $attachment_id = wp_insert_attachment( $attachment, $upload['file'] );
    
    if ( is_wp_error( $attachment_id ) ) {
        wp_send_json_error( array(
            'message' => $attachment_id->get_error_message(),
        ) );
    }
    
    $attachment_data = wp_generate_attachment_metadata( $attachment_id, $upload['file'] );
    wp_update_attachment_metadata( $attachment_id, $attachment_data );
    
    wp_send_json_success( array(
        'message'       => __( 'File uploaded successfully', 'wp-plugin-starter' ),
        'attachment_id' => $attachment_id,
        'url'           => wp_get_attachment_url( $attachment_id ),
    ) );
}
add_action( 'wp_ajax_upload_file', 'wp_plugin_starter_upload_file' );
```

**JavaScript with FormData:**
```javascript
$('#file-upload-form').on('submit', function(e) {
    e.preventDefault();
    
    var formData = new FormData();
    formData.append('action', 'upload_file');
    formData.append('nonce', wpPluginAjax.nonce);
    formData.append('file', $('#file-input')[0].files[0]);
    
    $.ajax({
        url: wpPluginAjax.ajax_url,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                alert(response.data.message);
                console.log('Attachment ID:', response.data.attachment_id);
            } else {
                alert('Error: ' + response.data.message);
            }
        }
    });
});
```

## Admin AJAX

AJAX in admin area:

```php
/**
 * Admin AJAX handler
 */
function wp_plugin_starter_admin_ajax() {
    check_ajax_referer( 'admin_nonce', 'nonce' );
    
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( 'Unauthorized' );
    }
    
    // Your admin logic here
    $option_value = get_option( 'my_option', 'default' );
    
    wp_send_json_success( array(
        'option_value' => $option_value,
    ) );
}
add_action( 'wp_ajax_admin_action', 'wp_plugin_starter_admin_ajax' );
```

## Heartbeat API

Use WordPress Heartbeat API:

```php
/**
 * Modify heartbeat data
 */
function wp_plugin_starter_heartbeat_received( $response, $data ) {
    if ( isset( $data['wp_plugin_starter_check'] ) ) {
        $response['wp_plugin_starter_data'] = array(
            'status' => 'active',
            'count'  => wp_count_posts()->publish,
        );
    }
    
    return $response;
}
add_filter( 'heartbeat_received', 'wp_plugin_starter_heartbeat_received', 10, 2 );
```

## Best Practices

### Security

1. **Always verify nonces** with `check_ajax_referer()`
2. **Check user capabilities** before processing
3. **Sanitize all inputs** from `$_POST` and `$_GET`
4. **Escape all outputs** before sending

### Performance

1. **Limit query results** to necessary data
2. **Use transients** to cache expensive operations
3. **Implement rate limiting** for public endpoints
4. **Return only required data** in responses

### Error Handling

```php
function wp_plugin_starter_safe_ajax() {
    try {
        check_ajax_referer( 'my_nonce', 'nonce' );
        
        // Your code here
        
        wp_send_json_success( $data );
        
    } catch ( Exception $e ) {
        error_log( 'AJAX Error: ' . $e->getMessage() );
        wp_send_json_error( array(
            'message' => __( 'An error occurred', 'wp-plugin-starter' ),
        ) );
    }
}
```

## Debugging AJAX

Enable debugging in JavaScript:

```javascript
$.ajax({
    // ... ajax config ...
    success: function(response) {
        console.log('Success:', response);
    },
    error: function(xhr, status, error) {
        console.error('AJAX Error:');
        console.error('Status:', status);
        console.error('Error:', error);
        console.error('Response:', xhr.responseText);
    }
});
```

## Related Documentation

- [Shortcodes](shortcodes) - Add AJAX functionality to shortcodes
- [Custom Post Types](custom-post-types) - Load custom post types via AJAX
