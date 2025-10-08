# Code Examples

This directory contains practical code examples for common WordPress plugin operations.

## Table of Contents
- [File Operations](#file-operations)
- [Data Looping](#data-looping)
- [Bulk Page Creation](#bulk-page-creation)

## File Operations

### Reading Various File Types

#### Reading a Text File

```php
/**
 * Read a text file from the plugin directory
 */
function wp_plugin_starter_read_text_file() {
    $file_path = WP_PLUGIN_STARTER_PATH . 'data/example.txt';
    
    // Check if file exists
    if ( ! file_exists( $file_path ) ) {
        return new WP_Error( 'file_not_found', __( 'File not found', 'wp-plugin-starter' ) );
    }
    
    // Read file contents
    $content = file_get_contents( $file_path );
    
    return $content;
}
```

#### Reading a CSV File

```php
/**
 * Read and parse a CSV file
 */
function wp_plugin_starter_read_csv_file() {
    $file_path = WP_PLUGIN_STARTER_PATH . 'data/example.csv';
    
    if ( ! file_exists( $file_path ) ) {
        return new WP_Error( 'file_not_found', __( 'File not found', 'wp-plugin-starter' ) );
    }
    
    $data = array();
    
    // Open file for reading
    if ( ( $handle = fopen( $file_path, 'r' ) ) !== false ) {
        // Read header row
        $header = fgetcsv( $handle, 1000, ',' );
        
        // Read data rows
        while ( ( $row = fgetcsv( $handle, 1000, ',' ) ) !== false ) {
            $data[] = array_combine( $header, $row );
        }
        
        fclose( $handle );
    }
    
    return $data;
}
```

#### Reading a JSON File

```php
/**
 * Read and parse a JSON file
 */
function wp_plugin_starter_read_json_file() {
    $file_path = WP_PLUGIN_STARTER_PATH . 'data/example.json';
    
    if ( ! file_exists( $file_path ) ) {
        return new WP_Error( 'file_not_found', __( 'File not found', 'wp-plugin-starter' ) );
    }
    
    // Read file contents
    $json_content = file_get_contents( $file_path );
    
    // Parse JSON
    $data = json_decode( $json_content, true );
    
    // Check for JSON errors
    if ( json_last_error() !== JSON_ERROR_NONE ) {
        return new WP_Error( 'json_error', json_last_error_msg() );
    }
    
    return $data;
}
```

#### Using WordPress Filesystem API (Recommended)

```php
/**
 * Read a file using WordPress Filesystem API
 */
function wp_plugin_starter_read_file_wp_filesystem() {
    // Initialize WordPress Filesystem
    global $wp_filesystem;
    
    if ( empty( $wp_filesystem ) ) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        WP_Filesystem();
    }
    
    $file_path = WP_PLUGIN_STARTER_PATH . 'data/example.txt';
    
    // Check if file exists
    if ( ! $wp_filesystem->exists( $file_path ) ) {
        return new WP_Error( 'file_not_found', __( 'File not found', 'wp-plugin-starter' ) );
    }
    
    // Read file contents
    $content = $wp_filesystem->get_contents( $file_path );
    
    return $content;
}
```

## Data Looping

### Looping Through Posts

```php
/**
 * Loop through posts with WP_Query
 */
function wp_plugin_starter_loop_posts() {
    $args = array(
        'post_type'      => 'post',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
    );
    
    $query = new WP_Query( $args );
    
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            
            // Access post data
            $post_id    = get_the_ID();
            $post_title = get_the_title();
            $post_content = get_the_content();
            
            // Do something with the post data
            echo '<h2>' . esc_html( $post_title ) . '</h2>';
        }
        
        wp_reset_postdata();
    }
}
```

### Looping Through Custom Post Types

```php
/**
 * Loop through custom post types
 */
function wp_plugin_starter_loop_custom_posts( $post_type = 'custom_type' ) {
    $args = array(
        'post_type'      => $post_type,
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
    );
    
    $query = new WP_Query( $args );
    
    $results = array();
    
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            
            $results[] = array(
                'id'      => get_the_ID(),
                'title'   => get_the_title(),
                'content' => get_the_content(),
                'meta'    => get_post_meta( get_the_ID() ),
            );
        }
        
        wp_reset_postdata();
    }
    
    return $results;
}
```

### Looping Through Array Data

```php
/**
 * Loop through array data with foreach
 */
function wp_plugin_starter_loop_array_data( $data ) {
    $processed_data = array();
    
    foreach ( $data as $key => $item ) {
        // Process each item
        $processed_data[] = array(
            'original_key' => $key,
            'value'        => $item,
            'processed'    => true,
        );
    }
    
    return $processed_data;
}
```

### Looping Through Taxonomies

```php
/**
 * Loop through taxonomy terms
 */
function wp_plugin_starter_loop_taxonomy_terms( $taxonomy = 'category' ) {
    $terms = get_terms( array(
        'taxonomy'   => $taxonomy,
        'hide_empty' => false,
    ) );
    
    if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
        foreach ( $terms as $term ) {
            echo '<div class="term-item">';
            echo '<h3>' . esc_html( $term->name ) . '</h3>';
            echo '<p>' . esc_html( $term->description ) . '</p>';
            echo '</div>';
        }
    }
}
```

## Bulk Page Creation

### Creating Pages from CSV Data

```php
/**
 * Create pages in bulk from CSV file
 */
function wp_plugin_starter_create_pages_from_csv() {
    $csv_data = wp_plugin_starter_read_csv_file();
    
    if ( is_wp_error( $csv_data ) ) {
        return $csv_data;
    }
    
    $created_pages = array();
    
    foreach ( $csv_data as $row ) {
        // Prepare page data
        $page_data = array(
            'post_title'   => sanitize_text_field( $row['title'] ),
            'post_content' => wp_kses_post( $row['content'] ),
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_author'  => get_current_user_id(),
        );
        
        // Insert the page
        $page_id = wp_insert_post( $page_data );
        
        if ( ! is_wp_error( $page_id ) ) {
            $created_pages[] = $page_id;
            
            // Add custom fields if present
            if ( isset( $row['meta_key'] ) && isset( $row['meta_value'] ) ) {
                update_post_meta( $page_id, sanitize_key( $row['meta_key'] ), sanitize_text_field( $row['meta_value'] ) );
            }
        }
    }
    
    return $created_pages;
}
```

### Creating Pages from JSON Data

```php
/**
 * Create pages in bulk from JSON file
 */
function wp_plugin_starter_create_pages_from_json() {
    $json_data = wp_plugin_starter_read_json_file();
    
    if ( is_wp_error( $json_data ) ) {
        return $json_data;
    }
    
    $created_pages = array();
    
    foreach ( $json_data as $page_info ) {
        $page_data = array(
            'post_title'   => sanitize_text_field( $page_info['title'] ),
            'post_content' => wp_kses_post( $page_info['content'] ),
            'post_status'  => isset( $page_info['status'] ) ? $page_info['status'] : 'publish',
            'post_type'    => 'page',
            'post_parent'  => isset( $page_info['parent_id'] ) ? absint( $page_info['parent_id'] ) : 0,
        );
        
        // Check if page already exists
        $existing_page = get_page_by_title( $page_data['post_title'], OBJECT, 'page' );
        
        if ( ! $existing_page ) {
            $page_id = wp_insert_post( $page_data );
            
            if ( ! is_wp_error( $page_id ) ) {
                $created_pages[] = $page_id;
                
                // Add metadata if present
                if ( isset( $page_info['meta'] ) && is_array( $page_info['meta'] ) ) {
                    foreach ( $page_info['meta'] as $meta_key => $meta_value ) {
                        update_post_meta( $page_id, sanitize_key( $meta_key ), $meta_value );
                    }
                }
            }
        }
    }
    
    return $created_pages;
}
```

### Creating Pages with Template Assignment

```php
/**
 * Create pages and assign templates
 */
function wp_plugin_starter_create_pages_with_template( $pages_data ) {
    $created_pages = array();
    
    foreach ( $pages_data as $page ) {
        $page_data = array(
            'post_title'   => sanitize_text_field( $page['title'] ),
            'post_content' => wp_kses_post( $page['content'] ),
            'post_status'  => 'publish',
            'post_type'    => 'page',
        );
        
        $page_id = wp_insert_post( $page_data );
        
        if ( ! is_wp_error( $page_id ) ) {
            // Assign page template
            if ( isset( $page['template'] ) ) {
                update_post_meta( $page_id, '_wp_page_template', sanitize_text_field( $page['template'] ) );
            }
            
            // Set featured image if provided
            if ( isset( $page['featured_image_url'] ) ) {
                $attachment_id = wp_plugin_starter_upload_image_from_url( $page['featured_image_url'] );
                if ( $attachment_id ) {
                    set_post_thumbnail( $page_id, $attachment_id );
                }
            }
            
            $created_pages[] = $page_id;
        }
    }
    
    return $created_pages;
}

/**
 * Helper function to upload image from URL
 */
function wp_plugin_starter_upload_image_from_url( $image_url ) {
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';
    
    $attachment_id = media_sideload_image( $image_url, 0, null, 'id' );
    
    if ( is_wp_error( $attachment_id ) ) {
        return false;
    }
    
    return $attachment_id;
}
```

### Batch Processing with Progress Tracking

```php
/**
 * Create pages in batches to avoid timeout
 */
function wp_plugin_starter_create_pages_batch( $data, $batch_size = 10 ) {
    $total = count( $data );
    $processed = get_option( 'wp_plugin_starter_processed', 0 );
    
    if ( $processed >= $total ) {
        delete_option( 'wp_plugin_starter_processed' );
        return array( 'completed' => true, 'total' => $total );
    }
    
    $batch_data = array_slice( $data, $processed, $batch_size );
    $created = array();
    
    foreach ( $batch_data as $page ) {
        $page_id = wp_insert_post( array(
            'post_title'   => sanitize_text_field( $page['title'] ),
            'post_content' => wp_kses_post( $page['content'] ),
            'post_status'  => 'publish',
            'post_type'    => 'page',
        ) );
        
        if ( ! is_wp_error( $page_id ) ) {
            $created[] = $page_id;
        }
        
        $processed++;
    }
    
    update_option( 'wp_plugin_starter_processed', $processed );
    
    return array(
        'completed' => false,
        'processed' => $processed,
        'total'     => $total,
        'created'   => $created,
    );
}
```
