---
sidebar_position: 7
---

# Data Looping

Master iterating through different WordPress data structures.

## Looping Through Posts

Use `WP_Query` to loop through posts efficiently:

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
            $post_id      = get_the_ID();
            $post_title   = get_the_title();
            $post_content = get_the_content();
            
            // Do something with the post data
            echo '<h2>' . esc_html( $post_title ) . '</h2>';
        }
        
        wp_reset_postdata();
    }
}
```

### Advanced Post Query

```php
function wp_plugin_starter_loop_posts_advanced() {
    $args = array(
        'post_type'      => 'post',
        'posts_per_page' => 10,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'category_name'  => 'news',
        'meta_query'     => array(
            array(
                'key'     => 'featured',
                'value'   => '1',
                'compare' => '='
            )
        ),
    );
    
    $query = new WP_Query( $args );
    
    if ( $query->have_posts() ) {
        $posts = array();
        
        while ( $query->have_posts() ) {
            $query->the_post();
            
            $posts[] = array(
                'id'       => get_the_ID(),
                'title'    => get_the_title(),
                'excerpt'  => get_the_excerpt(),
                'permalink' => get_permalink(),
            );
        }
        
        wp_reset_postdata();
        
        return $posts;
    }
    
    return array();
}
```

## Looping Through Custom Post Types

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

## Looping Through Array Data

### Basic Array Loop

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

### Nested Array Loop

```php
/**
 * Loop through nested array data
 */
function wp_plugin_starter_loop_nested_array( $data ) {
    foreach ( $data as $category => $items ) {
        echo '<h3>' . esc_html( $category ) . '</h3>';
        echo '<ul>';
        
        foreach ( $items as $item ) {
            echo '<li>' . esc_html( $item ) . '</li>';
        }
        
        echo '</ul>';
    }
}
```

### Array with Conditions

```php
/**
 * Loop through array with conditional processing
 */
function wp_plugin_starter_loop_array_conditional( $data ) {
    $filtered = array();
    
    foreach ( $data as $item ) {
        // Only process items that meet criteria
        if ( isset( $item['status'] ) && $item['status'] === 'active' ) {
            $filtered[] = array(
                'name'  => sanitize_text_field( $item['name'] ),
                'value' => absint( $item['value'] ),
            );
        }
    }
    
    return $filtered;
}
```

## Looping Through Taxonomies

### Get All Terms

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

### Get Terms with Posts

```php
/**
 * Loop through terms and their posts
 */
function wp_plugin_starter_loop_terms_with_posts( $taxonomy = 'category' ) {
    $terms = get_terms( array(
        'taxonomy'   => $taxonomy,
        'hide_empty' => true,
    ) );
    
    if ( is_wp_error( $terms ) || empty( $terms ) ) {
        return array();
    }
    
    $result = array();
    
    foreach ( $terms as $term ) {
        $posts = get_posts( array(
            'post_type'      => 'post',
            'posts_per_page' => 5,
            'tax_query'      => array(
                array(
                    'taxonomy' => $taxonomy,
                    'field'    => 'term_id',
                    'terms'    => $term->term_id,
                )
            ),
        ) );
        
        $result[ $term->name ] = array(
            'term_id'     => $term->term_id,
            'description' => $term->description,
            'post_count'  => $term->count,
            'posts'       => $posts,
        );
    }
    
    return $result;
}
```

## Looping Through Users

```php
/**
 * Loop through users
 */
function wp_plugin_starter_loop_users( $role = '' ) {
    $args = array(
        'orderby' => 'display_name',
        'order'   => 'ASC',
    );
    
    if ( ! empty( $role ) ) {
        $args['role'] = $role;
    }
    
    $users = get_users( $args );
    
    $result = array();
    
    foreach ( $users as $user ) {
        $result[] = array(
            'id'           => $user->ID,
            'name'         => $user->display_name,
            'email'        => $user->user_email,
            'roles'        => $user->roles,
        );
    }
    
    return $result;
}
```

## Performance Tips

### Pagination

For large datasets, use pagination:

```php
function wp_plugin_starter_loop_posts_paginated( $page = 1, $per_page = 20 ) {
    $args = array(
        'post_type'      => 'post',
        'posts_per_page' => $per_page,
        'paged'          => $page,
    );
    
    $query = new WP_Query( $args );
    
    return array(
        'posts'       => $query->posts,
        'total'       => $query->found_posts,
        'total_pages' => $query->max_num_pages,
        'current_page' => $page,
    );
}
```

### Caching Results

Cache expensive queries:

```php
function wp_plugin_starter_loop_cached() {
    $cache_key = 'my_cached_posts';
    $cached = get_transient( $cache_key );
    
    if ( false !== $cached ) {
        return $cached;
    }
    
    // Expensive query
    $results = wp_plugin_starter_loop_posts();
    
    // Cache for 1 hour
    set_transient( $cache_key, $results, HOUR_IN_SECONDS );
    
    return $results;
}
```

## Best Practices

1. **Always reset post data** after custom queries
2. **Use `posts_per_page`** instead of `-1` for large datasets
3. **Sanitize output** with `esc_html()`, `esc_attr()`, etc.
4. **Check for errors** with `is_wp_error()`
5. **Cache results** for expensive queries

## Related Examples

- [File Operations](file-operations) - Read data from files to loop through
- [Bulk Page Creation](bulk-page-creation) - Use loops to create content
