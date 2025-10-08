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

---

## Custom Page Templates in Plugins

### Understanding Plugin-Based Page Templates

Since WordPress 4.7+, plugins can register custom page templates that appear in the page template dropdown in the WordPress admin. This is done by using the `theme_page_templates` filter to add templates and the `template_include` filter to load them.

### Creating a Custom Page Template

#### Step 1: Register the Template

Add this to your main plugin file or in `includes/class-wp-plugin-starter.php`:

```php
/**
 * Register custom page templates
 * 
 * This function adds custom page templates to the page template dropdown
 * in the WordPress admin area when editing pages.
 */
function wp_plugin_starter_register_page_templates( $templates ) {
    // Add your custom template to the array
    // Key: template file path relative to plugin directory
    // Value: template name shown in admin dropdown
    $templates['templates/page-city-template.php'] = __( 'City Page Template', 'wp-plugin-starter' );
    
    return $templates;
}
add_filter( 'theme_page_templates', 'wp_plugin_starter_register_page_templates' );
```

#### Step 2: Load the Template

Add this function to intercept template loading:

```php
/**
 * Load custom page template from plugin
 * 
 * This function intercepts the template loading process and loads
 * our custom template from the plugin directory instead of the theme.
 */
function wp_plugin_starter_load_page_template( $template ) {
    // Get the current page ID
    $page_id = get_queried_object_id();
    
    // Get the template slug assigned to this page
    $page_template = get_post_meta( $page_id, '_wp_page_template', true );
    
    // Check if our custom template is assigned to this page
    if ( $page_template === 'templates/page-city-template.php' ) {
        // Build the full path to our template file
        $plugin_template = WP_PLUGIN_STARTER_PATH . $page_template;
        
        // Check if the template file exists
        if ( file_exists( $plugin_template ) ) {
            return $plugin_template;
        }
    }
    
    // Return the default template if ours isn't being used
    return $template;
}
add_filter( 'template_include', 'wp_plugin_starter_load_page_template' );
```

#### Step 3: Create the Template File

Create `templates/page-city-template.php` in your plugin directory:

```php
<?php
/**
 * Template Name: City Page Template
 * Template Post Type: page
 * Description: A custom template for displaying city information
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main city-page-template">
        
        <?php while ( have_posts() ) : the_post(); ?>
            
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                
                <header class="entry-header">
                    <h1 class="entry-title"><?php the_title(); ?></h1>
                    
                    <?php
                    // Display custom city metadata
                    $city_name = get_post_meta( get_the_ID(), 'city_name', true );
                    $state = get_post_meta( get_the_ID(), 'state', true );
                    $population = get_post_meta( get_the_ID(), 'population', true );
                    $country = get_post_meta( get_the_ID(), 'country', true );
                    ?>
                    
                    <?php if ( $city_name ) : ?>
                        <div class="city-metadata">
                            <h2><?php esc_html_e( 'City Information', 'wp-plugin-starter' ); ?></h2>
                            <ul class="city-details">
                                <li><strong><?php esc_html_e( 'City:', 'wp-plugin-starter' ); ?></strong> <?php echo esc_html( $city_name ); ?></li>
                                <?php if ( $state ) : ?>
                                    <li><strong><?php esc_html_e( 'State:', 'wp-plugin-starter' ); ?></strong> <?php echo esc_html( $state ); ?></li>
                                <?php endif; ?>
                                <?php if ( $country ) : ?>
                                    <li><strong><?php esc_html_e( 'Country:', 'wp-plugin-starter' ); ?></strong> <?php echo esc_html( $country ); ?></li>
                                <?php endif; ?>
                                <?php if ( $population ) : ?>
                                    <li><strong><?php esc_html_e( 'Population:', 'wp-plugin-starter' ); ?></strong> <?php echo esc_html( number_format( $population ) ); ?></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </header>
                
                <div class="entry-content">
                    <?php
                    // Display the main page content
                    the_content();
                    
                    // Display page links for multi-page content
                    wp_link_pages( array(
                        'before' => '<div class="page-links">' . __( 'Pages:', 'wp-plugin-starter' ),
                        'after'  => '</div>',
                    ) );
                    ?>
                </div>
                
                <footer class="entry-footer">
                    <?php
                    // You can add additional content here
                    // For example, related cities, maps, etc.
                    ?>
                </footer>
                
            </article>
            
            <?php
            // If comments are open or there are comments, load the comment template
            if ( comments_open() || get_comments_number() ) :
                comments_template();
            endif;
            ?>
            
        <?php endwhile; ?>
        
    </main>
</div>

<?php
get_sidebar();
get_footer();
```

### Creating Pages from CSV with Custom Template

Now let's create a function that reads a CSV file with city data and creates pages using our custom template:

#### Example CSV Structure

Create `data/cities.csv`:

```csv
city,state,country,population,description
New York,New York,USA,8336817,"The largest city in the United States, known for its significant impact on commerce, finance, media, art, fashion, research, technology, education, and entertainment."
Los Angeles,California,USA,3979576,"Known for its Mediterranean climate, ethnic and cultural diversity, Hollywood entertainment industry, and sprawling metropolitan area."
Chicago,Illinois,USA,2693976,"The third-most populous city in the United States, known for its bold architecture, museums, and vibrant arts scene."
Houston,Texas,USA,2320268,"The most populous city in Texas and the fourth-most populous city in the United States, known for its energy industry and NASA's Johnson Space Center."
Phoenix,Arizona,USA,1680992,"The capital and most populous city of Arizona, known for its year-round sun and warm temperatures."
```

#### CSV Import Function with Custom Template

```php
/**
 * Create city pages from CSV file with custom template
 * 
 * This function demonstrates how to:
 * 1. Read a CSV file with city data
 * 2. Create a page for each city
 * 3. Assign a custom page template to each page
 * 4. Store additional metadata for each city
 */
function wp_plugin_starter_create_city_pages_from_csv() {
    $file_path = WP_PLUGIN_STARTER_PATH . 'data/cities.csv';
    
    if ( ! file_exists( $file_path ) ) {
        return new WP_Error( 'file_not_found', __( 'CSV file not found', 'wp-plugin-starter' ) );
    }
    
    $created_pages = array();
    $errors = array();
    
    // Open the CSV file for reading
    if ( ( $handle = fopen( $file_path, 'r' ) ) !== false ) {
        // Read the first row to get column headers
        $headers = fgetcsv( $handle, 1000, ',' );
        
        // Normalize headers (trim whitespace, convert to lowercase)
        $headers = array_map( function( $header ) {
            return strtolower( trim( $header ) );
        }, $headers );
        
        // Read each subsequent row
        $row_number = 1;
        while ( ( $row = fgetcsv( $handle, 1000, ',' ) ) !== false ) {
            $row_number++;
            
            // Skip empty rows
            if ( empty( array_filter( $row ) ) ) {
                continue;
            }
            
            // Combine headers with row data to create associative array
            $city_data = array();
            foreach ( $headers as $index => $header ) {
                $city_data[ $header ] = isset( $row[ $index ] ) ? trim( $row[ $index ] ) : '';
            }
            
            // Validate required data
            if ( empty( $city_data['city'] ) ) {
                $errors[] = sprintf(
                    __( 'Row %d: City name is required.', 'wp-plugin-starter' ),
                    $row_number
                );
                continue;
            }
            
            $city_name = sanitize_text_field( $city_data['city'] );
            
            // Check if a page with this title already exists
            $existing_page = get_page_by_title( $city_name, OBJECT, 'page' );
            if ( $existing_page ) {
                $errors[] = sprintf(
                    __( 'Row %d: A page for "%s" already exists.', 'wp-plugin-starter' ),
                    $row_number,
                    $city_name
                );
                continue;
            }
            
            // Prepare page content
            $content = isset( $city_data['description'] ) ? $city_data['description'] : '';
            
            // Create the page
            $page_data = array(
                'post_title'   => $city_name,
                'post_content' => wp_kses_post( $content ),
                'post_status'  => 'publish',
                'post_type'    => 'page',
                'post_author'  => get_current_user_id(),
            );
            
            // Insert the page
            $page_id = wp_insert_post( $page_data );
            
            if ( is_wp_error( $page_id ) ) {
                $errors[] = sprintf(
                    __( 'Row %d: Error creating page for "%s": %s', 'wp-plugin-starter' ),
                    $row_number,
                    $city_name,
                    $page_id->get_error_message()
                );
                continue;
            }
            
            // Assign the custom page template
            update_post_meta( $page_id, '_wp_page_template', 'templates/page-city-template.php' );
            
            // Add custom metadata
            update_post_meta( $page_id, 'city_name', sanitize_text_field( $city_name ) );
            
            if ( ! empty( $city_data['state'] ) ) {
                update_post_meta( $page_id, 'state', sanitize_text_field( $city_data['state'] ) );
            }
            
            if ( ! empty( $city_data['country'] ) ) {
                update_post_meta( $page_id, 'country', sanitize_text_field( $city_data['country'] ) );
            }
            
            if ( ! empty( $city_data['population'] ) ) {
                // Remove any commas and convert to integer
                $population = absint( str_replace( ',', '', $city_data['population'] ) );
                update_post_meta( $page_id, 'population', $population );
            }
            
            $created_pages[] = $page_id;
        }
        
        fclose( $handle );
    }
    
    return array(
        'created' => $created_pages,
        'errors'  => $errors,
    );
}
```

### Admin Interface for CSV Import

To make this functionality user-friendly, create an admin page with a form:

```php
/**
 * Add admin menu for CSV importer
 */
function wp_plugin_starter_add_csv_importer_menu() {
    add_menu_page(
        __( 'City CSV Importer', 'wp-plugin-starter' ),
        __( 'City Importer', 'wp-plugin-starter' ),
        'manage_options',
        'wp-plugin-starter-csv-importer',
        'wp_plugin_starter_csv_importer_page',
        'dashicons-upload',
        30
    );
}
add_action( 'admin_menu', 'wp_plugin_starter_add_csv_importer_menu' );

/**
 * Display the CSV importer admin page
 */
function wp_plugin_starter_csv_importer_page() {
    // Check user capabilities
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( __( 'You do not have sufficient permissions to access this page.', 'wp-plugin-starter' ) );
    }
    
    // Handle form submission
    if ( isset( $_POST['wp_plugin_starter_csv_import'] ) && check_admin_referer( 'wp_plugin_starter_csv_import_action', 'wp_plugin_starter_csv_import_nonce' ) ) {
        
        // Verify file was uploaded
        if ( isset( $_FILES['csv_file'] ) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK ) {
            
            // Verify file type
            $file_info = pathinfo( $_FILES['csv_file']['name'] );
            if ( strtolower( $file_info['extension'] ) === 'csv' ) {
                
                // Move uploaded file to data directory
                $upload_path = WP_PLUGIN_STARTER_PATH . 'data/cities.csv';
                if ( move_uploaded_file( $_FILES['csv_file']['tmp_name'], $upload_path ) ) {
                    
                    // Process the CSV file
                    $result = wp_plugin_starter_create_city_pages_from_csv();
                    
                    // Display results
                    if ( ! empty( $result['created'] ) ) {
                        echo '<div class="notice notice-success"><p>';
                        printf(
                            _n(
                                '%d page created successfully!',
                                '%d pages created successfully!',
                                count( $result['created'] ),
                                'wp-plugin-starter'
                            ),
                            count( $result['created'] )
                        );
                        echo '</p></div>';
                    }
                    
                    if ( ! empty( $result['errors'] ) ) {
                        echo '<div class="notice notice-error"><p>';
                        echo '<strong>' . __( 'Errors:', 'wp-plugin-starter' ) . '</strong><br>';
                        echo implode( '<br>', array_map( 'esc_html', $result['errors'] ) );
                        echo '</p></div>';
                    }
                    
                } else {
                    echo '<div class="notice notice-error"><p>' . __( 'Error moving uploaded file.', 'wp-plugin-starter' ) . '</p></div>';
                }
            } else {
                echo '<div class="notice notice-error"><p>' . __( 'Please upload a valid CSV file.', 'wp-plugin-starter' ) . '</p></div>';
            }
        } else {
            echo '<div class="notice notice-error"><p>' . __( 'Error uploading file.', 'wp-plugin-starter' ) . '</p></div>';
        }
    }
    
    // Display the form
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__( 'Import Cities from CSV', 'wp-plugin-starter' ); ?></h1>
        
        <div class="card">
            <h2><?php echo esc_html__( 'CSV File Format', 'wp-plugin-starter' ); ?></h2>
            <p><?php echo esc_html__( 'Your CSV file should have the following columns:', 'wp-plugin-starter' ); ?></p>
            <ul>
                <li><strong>city</strong> - <?php esc_html_e( 'The city name (required)', 'wp-plugin-starter' ); ?></li>
                <li><strong>state</strong> - <?php esc_html_e( 'The state name (optional)', 'wp-plugin-starter' ); ?></li>
                <li><strong>country</strong> - <?php esc_html_e( 'The country name (optional)', 'wp-plugin-starter' ); ?></li>
                <li><strong>population</strong> - <?php esc_html_e( 'The population number (optional)', 'wp-plugin-starter' ); ?></li>
                <li><strong>description</strong> - <?php esc_html_e( 'A description of the city (optional)', 'wp-plugin-starter' ); ?></li>
            </ul>
            <p><strong><?php echo esc_html__( 'Note:', 'wp-plugin-starter' ); ?></strong> 
            <?php echo esc_html__( 'The first row should contain column headers. Each subsequent row will create a new page.', 'wp-plugin-starter' ); ?></p>
        </div>
        
        <form method="post" enctype="multipart/form-data">
            <?php wp_nonce_field( 'wp_plugin_starter_csv_import_action', 'wp_plugin_starter_csv_import_nonce' ); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="csv_file"><?php echo esc_html__( 'CSV File', 'wp-plugin-starter' ); ?></label>
                    </th>
                    <td>
                        <input type="file" name="csv_file" id="csv_file" accept=".csv" required>
                        <p class="description">
                            <?php echo esc_html__( 'Select a CSV file to import. Maximum file size: 2MB', 'wp-plugin-starter' ); ?>
                        </p>
                    </td>
                </tr>
            </table>
            
            <?php submit_button( __( 'Import Cities', 'wp-plugin-starter' ), 'primary', 'wp_plugin_starter_csv_import' ); ?>
        </form>
    </div>
    <?php
}
```

### Key Concepts Explained

#### 1. Template Registration

The `theme_page_templates` filter allows plugins to add templates to the dropdown in the page editor. The key is the template path, and the value is the display name.

#### 2. Template Loading

The `template_include` filter intercepts template loading. By checking the `_wp_page_template` meta key, we can determine which template is assigned and return our plugin's template file.

#### 3. CSV Parsing with City Column

The CSV parsing uses `fgetcsv()` to read rows. The first row becomes headers, and subsequent rows are data. The `city` column is required and becomes the page title.

#### 4. Metadata Storage

Custom fields are stored using `update_post_meta()`. The special `_wp_page_template` meta key tells WordPress which template to use. Other metadata (city_name, state, population) can be retrieved in the template using `get_post_meta()`.

#### 5. Data Validation

Always validate and sanitize data:
- `sanitize_text_field()` for text inputs
- `wp_kses_post()` for content that may contain HTML
- `absint()` for integers
- Check for required fields before processing

### Advanced: WP-CLI Command

For command-line CSV imports:

```php
if ( defined( 'WP_CLI' ) && WP_CLI ) {
    /**
     * Import cities from CSV file
     *
     * ## OPTIONS
     *
     * <file>
     * : Path to the CSV file
     *
     * ## EXAMPLES
     *
     *     wp cities import path/to/cities.csv
     */
    WP_CLI::add_command( 'cities import', function( $args ) {
        $file = $args[0];
        
        if ( ! file_exists( $file ) ) {
            WP_CLI::error( "File not found: $file" );
        }
        
        // Copy file to plugin data directory
        $target = WP_PLUGIN_STARTER_PATH . 'data/cities.csv';
        copy( $file, $target );
        
        // Process import
        $result = wp_plugin_starter_create_city_pages_from_csv();
        
        if ( ! empty( $result['created'] ) ) {
            WP_CLI::success( sprintf( 'Created %d pages.', count( $result['created'] ) ) );
        }
        
        if ( ! empty( $result['errors'] ) ) {
            WP_CLI::warning( 'Errors occurred:' );
            foreach ( $result['errors'] as $error ) {
                WP_CLI::log( '  - ' . $error );
            }
        }
    } );
}
```

This comprehensive example demonstrates how to create custom page templates in a WordPress plugin and use them with CSV data imports, specifically using the city column as the primary identifier for creating multiple pages.
