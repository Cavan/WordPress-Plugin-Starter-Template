---
sidebar_position: 9
---

# Custom Post Types

Learn how to register and work with custom post types in your WordPress plugin.

## Basic Custom Post Type Registration

Register a simple custom post type:

```php
/**
 * Register a custom post type
 */
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

## Advanced Custom Post Type

Register a fully-featured custom post type:

```php
/**
 * Register an advanced custom post type
 */
function wp_plugin_starter_register_advanced_cpt() {
    $labels = array(
        'name'                  => _x( 'Projects', 'Post type general name', 'wp-plugin-starter' ),
        'singular_name'         => _x( 'Project', 'Post type singular name', 'wp-plugin-starter' ),
        'menu_name'             => _x( 'Projects', 'Admin Menu text', 'wp-plugin-starter' ),
        'add_new'               => __( 'Add New', 'wp-plugin-starter' ),
        'add_new_item'          => __( 'Add New Project', 'wp-plugin-starter' ),
        'new_item'              => __( 'New Project', 'wp-plugin-starter' ),
        'edit_item'             => __( 'Edit Project', 'wp-plugin-starter' ),
        'view_item'             => __( 'View Project', 'wp-plugin-starter' ),
        'all_items'             => __( 'All Projects', 'wp-plugin-starter' ),
        'search_items'          => __( 'Search Projects', 'wp-plugin-starter' ),
        'not_found'             => __( 'No projects found.', 'wp-plugin-starter' ),
        'not_found_in_trash'    => __( 'No projects found in Trash.', 'wp-plugin-starter' ),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'project' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 20,
        'menu_icon'          => 'dashicons-portfolio',
        'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields' ),
        'show_in_rest'       => true, // Enable Gutenberg editor
    );

    register_post_type( 'project', $args );
}
add_action( 'init', 'wp_plugin_starter_register_advanced_cpt' );
```

## Custom Taxonomy for Custom Post Type

Add a custom taxonomy to your custom post type:

```php
/**
 * Register a custom taxonomy
 */
function wp_plugin_starter_register_taxonomy() {
    $labels = array(
        'name'              => _x( 'Project Categories', 'taxonomy general name', 'wp-plugin-starter' ),
        'singular_name'     => _x( 'Project Category', 'taxonomy singular name', 'wp-plugin-starter' ),
        'search_items'      => __( 'Search Project Categories', 'wp-plugin-starter' ),
        'all_items'         => __( 'All Project Categories', 'wp-plugin-starter' ),
        'parent_item'       => __( 'Parent Project Category', 'wp-plugin-starter' ),
        'parent_item_colon' => __( 'Parent Project Category:', 'wp-plugin-starter' ),
        'edit_item'         => __( 'Edit Project Category', 'wp-plugin-starter' ),
        'update_item'       => __( 'Update Project Category', 'wp-plugin-starter' ),
        'add_new_item'      => __( 'Add New Project Category', 'wp-plugin-starter' ),
        'new_item_name'     => __( 'New Project Category Name', 'wp-plugin-starter' ),
        'menu_name'         => __( 'Project Category', 'wp-plugin-starter' ),
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'project-category' ),
        'show_in_rest'      => true,
    );

    register_taxonomy( 'project_category', array( 'project' ), $args );
}
add_action( 'init', 'wp_plugin_starter_register_taxonomy' );
```

## Adding Custom Meta Boxes

Add custom fields to your custom post type:

```php
/**
 * Add custom meta box
 */
function wp_plugin_starter_add_meta_box() {
    add_meta_box(
        'project_details',
        __( 'Project Details', 'wp-plugin-starter' ),
        'wp_plugin_starter_render_meta_box',
        'project',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'wp_plugin_starter_add_meta_box' );

/**
 * Render the meta box
 */
function wp_plugin_starter_render_meta_box( $post ) {
    wp_nonce_field( 'wp_plugin_starter_save_meta', 'wp_plugin_starter_meta_nonce' );
    
    $client = get_post_meta( $post->ID, '_project_client', true );
    $date = get_post_meta( $post->ID, '_project_date', true );
    ?>
    <p>
        <label for="project_client"><?php _e( 'Client Name:', 'wp-plugin-starter' ); ?></label>
        <input type="text" id="project_client" name="project_client" value="<?php echo esc_attr( $client ); ?>" style="width: 100%;" />
    </p>
    <p>
        <label for="project_date"><?php _e( 'Completion Date:', 'wp-plugin-starter' ); ?></label>
        <input type="date" id="project_date" name="project_date" value="<?php echo esc_attr( $date ); ?>" />
    </p>
    <?php
}

/**
 * Save meta box data
 */
function wp_plugin_starter_save_meta_box( $post_id ) {
    if ( ! isset( $_POST['wp_plugin_starter_meta_nonce'] ) ) {
        return;
    }

    if ( ! wp_verify_nonce( $_POST['wp_plugin_starter_meta_nonce'], 'wp_plugin_starter_save_meta' ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    if ( isset( $_POST['project_client'] ) ) {
        update_post_meta( $post_id, '_project_client', sanitize_text_field( $_POST['project_client'] ) );
    }

    if ( isset( $_POST['project_date'] ) ) {
        update_post_meta( $post_id, '_project_date', sanitize_text_field( $_POST['project_date'] ) );
    }
}
add_action( 'save_post_project', 'wp_plugin_starter_save_meta_box' );
```

## Querying Custom Post Types

Query your custom post type:

```php
/**
 * Get all projects
 */
function wp_plugin_starter_get_projects( $args = array() ) {
    $defaults = array(
        'post_type'      => 'project',
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'DESC',
    );

    $args = wp_parse_args( $args, $defaults );
    $query = new WP_Query( $args );

    return $query->posts;
}

/**
 * Get projects by category
 */
function wp_plugin_starter_get_projects_by_category( $category_slug ) {
    $args = array(
        'post_type' => 'project',
        'tax_query' => array(
            array(
                'taxonomy' => 'project_category',
                'field'    => 'slug',
                'terms'    => $category_slug,
            ),
        ),
    );

    return wp_plugin_starter_get_projects( $args );
}
```

## Custom Post Type Templates

Create custom templates for your post type:

### In Your Theme

Create a file named `single-project.php` in your theme:

```php
<?php
/**
 * Template for single project
 */
get_header();

while ( have_posts() ) :
    the_post();
    
    $client = get_post_meta( get_the_ID(), '_project_client', true );
    $date = get_post_meta( get_the_ID(), '_project_date', true );
    ?>
    
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <h1><?php the_title(); ?></h1>
        
        <?php if ( $client ) : ?>
            <p><strong>Client:</strong> <?php echo esc_html( $client ); ?></p>
        <?php endif; ?>
        
        <?php if ( $date ) : ?>
            <p><strong>Date:</strong> <?php echo esc_html( $date ); ?></p>
        <?php endif; ?>
        
        <div class="project-content">
            <?php the_content(); ?>
        </div>
    </article>
    
    <?php
endwhile;

get_footer();
```

## Admin Customization

Customize the admin columns:

```php
/**
 * Add custom columns to admin
 */
function wp_plugin_starter_add_columns( $columns ) {
    $new_columns = array();
    
    foreach ( $columns as $key => $value ) {
        $new_columns[ $key ] = $value;
        
        if ( $key === 'title' ) {
            $new_columns['client'] = __( 'Client', 'wp-plugin-starter' );
            $new_columns['project_category'] = __( 'Category', 'wp-plugin-starter' );
        }
    }
    
    return $new_columns;
}
add_filter( 'manage_project_posts_columns', 'wp_plugin_starter_add_columns' );

/**
 * Populate custom columns
 */
function wp_plugin_starter_populate_columns( $column, $post_id ) {
    switch ( $column ) {
        case 'client':
            $client = get_post_meta( $post_id, '_project_client', true );
            echo $client ? esc_html( $client ) : '—';
            break;
            
        case 'project_category':
            $terms = get_the_terms( $post_id, 'project_category' );
            if ( $terms && ! is_wp_error( $terms ) ) {
                $term_names = array();
                foreach ( $terms as $term ) {
                    $term_names[] = $term->name;
                }
                echo esc_html( implode( ', ', $term_names ) );
            } else {
                echo '—';
            }
            break;
    }
}
add_action( 'manage_project_posts_custom_column', 'wp_plugin_starter_populate_columns', 10, 2 );
```

## Best Practices

1. **Use proper text domains** for all translatable strings
2. **Sanitize and validate** all user inputs
3. **Use nonces** for form submissions
4. **Check capabilities** before saving data
5. **Flush rewrite rules** after registering (only on activation)

## Flushing Rewrite Rules

Add to your activation hook:

```php
function wp_plugin_starter_activate() {
    wp_plugin_starter_register_post_type();
    wp_plugin_starter_register_taxonomy();
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'wp_plugin_starter_activate' );
```

## Related Documentation

- [Shortcodes](shortcodes) - Create shortcodes to display custom post types
- [AJAX Handlers](ajax-handlers) - Load custom post types via AJAX
