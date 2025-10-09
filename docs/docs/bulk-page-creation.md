---
sidebar_position: 8
---

# Bulk Page Creation

Learn how to create multiple pages programmatically from various data sources.

## Creating Pages from CSV Data

Read a CSV file and create pages with metadata:

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
                update_post_meta( 
                    $page_id, 
                    sanitize_key( $row['meta_key'] ), 
                    sanitize_text_field( $row['meta_value'] ) 
                );
            }
        }
    }
    
    return $created_pages;
}
```

**CSV Format:**
```csv
title,content,meta_key,meta_value
"About Us","This is the about us page content","page_layout","full-width"
"Contact","Contact us at...","sidebar_position","right"
```

## Creating Pages from JSON Data

Parse JSON and create pages with hierarchical structure:

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

**JSON Format:**
```json
[
  {
    "title": "Products",
    "content": "Our products page",
    "status": "publish",
    "parent_id": 0,
    "meta": {
      "sidebar": "product-sidebar",
      "featured": "1"
    }
  },
  {
    "title": "Product Category",
    "content": "Category content",
    "status": "publish",
    "parent_id": 123
  }
]
```

## Creating Pages with Template Assignment

Assign specific page templates during creation:

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
                update_post_meta( 
                    $page_id, 
                    '_wp_page_template', 
                    sanitize_text_field( $page['template'] ) 
                );
            }
            
            // Set featured image if provided
            if ( isset( $page['featured_image_url'] ) ) {
                $attachment_id = wp_plugin_starter_upload_image_from_url( 
                    $page['featured_image_url'] 
                );
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

## Batch Processing with Progress Tracking

Process large datasets in batches to avoid timeouts:

```php
/**
 * Create pages in batches to avoid timeout
 */
function wp_plugin_starter_create_pages_batch( $data, $batch_size = 10 ) {
    $total = count( $data );
    $processed = get_option( 'wp_plugin_starter_processed', 0 );
    
    if ( $processed >= $total ) {
        delete_option( 'wp_plugin_starter_processed' );
        return array( 
            'completed' => true, 
            'total' => $total 
        );
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

### AJAX Batch Processing

Process batches via AJAX for better user feedback:

```php
/**
 * AJAX handler for batch processing
 */
function wp_plugin_starter_ajax_batch_create() {
    check_ajax_referer( 'batch_create_nonce', 'nonce' );
    
    if ( ! current_user_can( 'edit_pages' ) ) {
        wp_send_json_error( 'Insufficient permissions' );
    }
    
    // Get data from transient or session
    $data = get_transient( 'bulk_pages_data' );
    
    if ( ! $data ) {
        wp_send_json_error( 'No data found' );
    }
    
    $result = wp_plugin_starter_create_pages_batch( $data, 10 );
    
    wp_send_json_success( $result );
}
add_action( 'wp_ajax_batch_create_pages', 'wp_plugin_starter_ajax_batch_create' );
```

## Creating Hierarchical Pages

Build page hierarchies programmatically:

```php
/**
 * Create parent and child pages
 */
function wp_plugin_starter_create_hierarchical_pages() {
    // Create parent page
    $parent_id = wp_insert_post( array(
        'post_title'   => 'Services',
        'post_content' => 'Our services overview',
        'post_status'  => 'publish',
        'post_type'    => 'page',
    ) );
    
    if ( is_wp_error( $parent_id ) ) {
        return $parent_id;
    }
    
    // Create child pages
    $children = array(
        'Web Design',
        'SEO Services',
        'Content Writing',
    );
    
    $created = array( 'parent' => $parent_id, 'children' => array() );
    
    foreach ( $children as $child_title ) {
        $child_id = wp_insert_post( array(
            'post_title'   => $child_title,
            'post_content' => 'Content for ' . $child_title,
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_parent'  => $parent_id,
        ) );
        
        if ( ! is_wp_error( $child_id ) ) {
            $created['children'][] = $child_id;
        }
    }
    
    return $created;
}
```

## Error Handling and Logging

Implement robust error handling:

```php
/**
 * Create pages with detailed error logging
 */
function wp_plugin_starter_create_pages_with_logging( $pages_data ) {
    $results = array(
        'success' => array(),
        'errors'  => array(),
    );
    
    foreach ( $pages_data as $index => $page ) {
        $page_id = wp_insert_post( array(
            'post_title'   => sanitize_text_field( $page['title'] ),
            'post_content' => wp_kses_post( $page['content'] ),
            'post_status'  => 'publish',
            'post_type'    => 'page',
        ), true );
        
        if ( is_wp_error( $page_id ) ) {
            $results['errors'][] = array(
                'index'   => $index,
                'title'   => $page['title'],
                'message' => $page_id->get_error_message(),
            );
            
            error_log( sprintf(
                'Failed to create page "%s": %s',
                $page['title'],
                $page_id->get_error_message()
            ) );
        } else {
            $results['success'][] = array(
                'id'    => $page_id,
                'title' => $page['title'],
            );
        }
    }
    
    return $results;
}
```

## Best Practices

### Performance

1. **Use batching** for large datasets (>100 pages)
2. **Disable autosave** temporarily: `define( 'AUTOSAVE_INTERVAL', 300 )`
3. **Increase memory limit** if needed
4. **Process during off-peak** hours

### Data Validation

```php
function wp_plugin_starter_validate_page_data( $page_data ) {
    $errors = array();
    
    if ( empty( $page_data['title'] ) ) {
        $errors[] = 'Title is required';
    }
    
    if ( empty( $page_data['content'] ) ) {
        $errors[] = 'Content is required';
    }
    
    if ( isset( $page_data['status'] ) ) {
        $valid_statuses = array( 'publish', 'draft', 'pending', 'private' );
        if ( ! in_array( $page_data['status'], $valid_statuses ) ) {
            $errors[] = 'Invalid post status';
        }
    }
    
    return empty( $errors ) ? true : $errors;
}
```

### Preventing Duplicates

```php
function wp_plugin_starter_page_exists( $title ) {
    $existing = get_page_by_title( $title, OBJECT, 'page' );
    return ! is_null( $existing );
}

// Before creating
if ( ! wp_plugin_starter_page_exists( $page_data['title'] ) ) {
    wp_insert_post( $page_data );
}
```

## Testing

Test with small datasets first:

```php
// Test data
$test_data = array(
    array(
        'title'   => 'Test Page 1',
        'content' => 'Test content 1',
    ),
    array(
        'title'   => 'Test Page 2',
        'content' => 'Test content 2',
    ),
);

$result = wp_plugin_starter_create_pages_from_json( $test_data );
```

## Related Examples

- [File Operations](file-operations) - Read data files for bulk creation
- [Data Looping](data-looping) - Iterate through data efficiently
