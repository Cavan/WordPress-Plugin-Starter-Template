---
sidebar_position: 10
---

# Shortcodes

Learn how to create and use shortcodes in your WordPress plugin.

## Basic Shortcode

Create a simple shortcode:

```php
/**
 * Register shortcodes
 */
function wp_plugin_starter_register_shortcodes() {
    add_shortcode( 'my_shortcode', 'wp_plugin_starter_my_shortcode_callback' );
}
add_action( 'init', 'wp_plugin_starter_register_shortcodes' );

/**
 * Shortcode callback
 */
function wp_plugin_starter_my_shortcode_callback( $atts ) {
    return '<div class="my-shortcode">Hello from shortcode!</div>';
}
```

**Usage:** `[my_shortcode]`

## Shortcode with Attributes

Create a shortcode that accepts parameters:

```php
/**
 * Shortcode with attributes
 */
function wp_plugin_starter_greeting_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'name' => 'Guest',
        'message' => 'Welcome',
    ), $atts, 'greeting' );
    
    return sprintf(
        '<div class="greeting">%s, %s!</div>',
        esc_html( $atts['message'] ),
        esc_html( $atts['name'] )
    );
}
add_shortcode( 'greeting', 'wp_plugin_starter_greeting_shortcode' );
```

**Usage:** `[greeting name="John" message="Hello"]`

## Shortcode with Content

Create a shortcode that wraps content:

```php
/**
 * Shortcode with enclosed content
 */
function wp_plugin_starter_box_shortcode( $atts, $content = null ) {
    $atts = shortcode_atts( array(
        'color' => 'blue',
        'title' => '',
    ), $atts, 'box' );
    
    $output = '<div class="custom-box" style="border: 2px solid ' . esc_attr( $atts['color'] ) . ';">';
    
    if ( ! empty( $atts['title'] ) ) {
        $output .= '<h3>' . esc_html( $atts['title'] ) . '</h3>';
    }
    
    $output .= '<div class="box-content">' . do_shortcode( $content ) . '</div>';
    $output .= '</div>';
    
    return $output;
}
add_shortcode( 'box', 'wp_plugin_starter_box_shortcode' );
```

**Usage:** `[box color="red" title="Notice"]Your content here[/box]`

## Advanced: Display Posts Shortcode

Display posts with various options:

```php
/**
 * Display posts shortcode
 */
function wp_plugin_starter_posts_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'type'     => 'post',
        'count'    => 5,
        'category' => '',
        'orderby'  => 'date',
        'order'    => 'DESC',
    ), $atts, 'display_posts' );
    
    $query_args = array(
        'post_type'      => sanitize_text_field( $atts['type'] ),
        'posts_per_page' => absint( $atts['count'] ),
        'orderby'        => sanitize_text_field( $atts['orderby'] ),
        'order'          => sanitize_text_field( $atts['order'] ),
    );
    
    if ( ! empty( $atts['category'] ) ) {
        $query_args['category_name'] = sanitize_text_field( $atts['category'] );
    }
    
    $query = new WP_Query( $query_args );
    
    if ( ! $query->have_posts() ) {
        return '<p>No posts found.</p>';
    }
    
    $output = '<div class="shortcode-posts">';
    
    while ( $query->have_posts() ) {
        $query->the_post();
        
        $output .= '<article class="post-item">';
        $output .= '<h3><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></h3>';
        $output .= '<div class="excerpt">' . get_the_excerpt() . '</div>';
        $output .= '</article>';
    }
    
    $output .= '</div>';
    
    wp_reset_postdata();
    
    return $output;
}
add_shortcode( 'display_posts', 'wp_plugin_starter_posts_shortcode' );
```

**Usage:** `[display_posts type="post" count="3" category="news"]`

## Shortcode in a Class

Organize shortcodes within a class (for use in `/public/class-public.php`):

```php
class WP_Plugin_Starter_Public {
    
    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        
        // Register shortcodes
        add_shortcode( 'my_shortcode', array( $this, 'my_shortcode_callback' ) );
    }
    
    public function my_shortcode_callback( $atts ) {
        $atts = shortcode_atts( array(
            'title' => 'Default Title',
        ), $atts );
        
        return '<div class="my-shortcode">' . esc_html( $atts['title'] ) . '</div>';
    }
}
```

## Enqueuing Assets for Shortcodes

Load CSS/JS only when shortcode is used:

```php
/**
 * Check if shortcode exists in content
 */
function wp_plugin_starter_has_shortcode() {
    global $post;
    
    if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'my_shortcode' ) ) {
        wp_enqueue_style( 'my-shortcode-style', plugin_dir_url( __FILE__ ) . 'css/shortcode.css' );
        wp_enqueue_script( 'my-shortcode-script', plugin_dir_url( __FILE__ ) . 'js/shortcode.js', array( 'jquery' ), '1.0', true );
    }
}
add_action( 'wp_enqueue_scripts', 'wp_plugin_starter_has_shortcode' );
```

## Shortcode with Template

Use a template file for complex output:

```php
/**
 * Shortcode using template
 */
function wp_plugin_starter_template_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'id' => 0,
    ), $atts );
    
    ob_start();
    
    // Load template
    $template = plugin_dir_path( __FILE__ ) . 'templates/shortcode-template.php';
    
    if ( file_exists( $template ) ) {
        // Make atts available to template
        set_query_var( 'shortcode_atts', $atts );
        include $template;
    }
    
    return ob_get_clean();
}
add_shortcode( 'template_shortcode', 'wp_plugin_starter_template_shortcode' );
```

**Template file** (`templates/shortcode-template.php`):
```php
<?php
$atts = get_query_var( 'shortcode_atts' );
?>
<div class="template-shortcode">
    <p>ID: <?php echo absint( $atts['id'] ); ?></p>
</div>
```

## Nested Shortcodes

Handle nested shortcodes properly:

```php
/**
 * Parent shortcode
 */
function wp_plugin_starter_tabs_shortcode( $atts, $content = null ) {
    return '<div class="tabs">' . do_shortcode( $content ) . '</div>';
}
add_shortcode( 'tabs', 'wp_plugin_starter_tabs_shortcode' );

/**
 * Child shortcode
 */
function wp_plugin_starter_tab_shortcode( $atts, $content = null ) {
    $atts = shortcode_atts( array(
        'title' => 'Tab',
    ), $atts );
    
    return '<div class="tab" data-title="' . esc_attr( $atts['title'] ) . '">' . 
           do_shortcode( $content ) . 
           '</div>';
}
add_shortcode( 'tab', 'wp_plugin_starter_tab_shortcode' );
```

**Usage:**
```
[tabs]
    [tab title="First Tab"]Content 1[/tab]
    [tab title="Second Tab"]Content 2[/tab]
[/tabs]
```

## AJAX Shortcode

Create a shortcode that loads content via AJAX:

```php
/**
 * AJAX shortcode
 */
function wp_plugin_starter_ajax_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'category' => '',
    ), $atts );
    
    wp_enqueue_script( 'ajax-shortcode', plugin_dir_url( __FILE__ ) . 'js/ajax-shortcode.js', array( 'jquery' ), '1.0', true );
    
    wp_localize_script( 'ajax-shortcode', 'ajaxShortcode', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'ajax_shortcode_nonce' ),
        'category' => $atts['category'],
    ) );
    
    return '<div id="ajax-content" data-category="' . esc_attr( $atts['category'] ) . '">
                <button id="load-content">Load Content</button>
                <div id="content-container"></div>
            </div>';
}
add_shortcode( 'ajax_content', 'wp_plugin_starter_ajax_shortcode' );
```

See [AJAX Handlers](ajax-handlers) for the AJAX callback.

## Best Practices

1. **Always sanitize and escape** output
2. **Use `shortcode_atts()`** for default values
3. **Return, don't echo** output
4. **Use unique shortcode names** to avoid conflicts
5. **Process nested shortcodes** with `do_shortcode()`
6. **Enqueue assets conditionally** when shortcode is present

## Testing Shortcodes

```php
// Test in a PHP file
$output = do_shortcode( '[my_shortcode title="Test"]' );
var_dump( $output );

// Test with attributes
$atts = array(
    'title' => 'Test Title',
    'color' => 'red',
);
$output = wp_plugin_starter_my_shortcode_callback( $atts );
```

## Related Documentation

- [AJAX Handlers](ajax-handlers) - Add AJAX functionality to shortcodes
- [Custom Post Types](custom-post-types) - Display custom post types via shortcodes
