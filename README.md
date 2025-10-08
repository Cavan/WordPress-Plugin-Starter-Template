# WordPress Scaffolding Plugin

## Purpose
The purpose of this project is to provide a template for WordPress plugin development. This should only be a bare bones template that adheres to the WordPress folder structure standard. I want this to be a good starting point for anyone who wants to create a new plugin with all the necessary boilerplate.

## Table of Contents

- [Custom Page Templates](#custom-page-templates)
- [Creating Pages from CSV Data](#creating-pages-from-csv-data)
- [Complete Working Example](#complete-working-example)
- [Resources](#resources)

## Custom Page Templates

Custom page templates allow you to create unique layouts for specific pages in WordPress. When creating custom page templates within a plugin, you need to understand how WordPress loads and recognizes templates.

### Understanding WordPress Page Templates

WordPress looks for page templates in two locations:
1. **Theme directory** - Traditional location for templates
2. **Plugin directory** - Since WordPress 4.7+, plugins can register page templates

### Creating a Custom Page Template in a Plugin

#### Step 1: Plugin Structure

First, create your plugin with the following structure:

```
my-custom-plugin/
├── my-custom-plugin.php (main plugin file)
├── templates/
│   └── page-city-template.php (custom page template)
└── includes/
    └── csv-importer.php (CSV import functionality)
```

#### Step 2: Main Plugin File

Create `my-custom-plugin.php` with the following code:

```php
<?php
/**
 * Plugin Name: My Custom Plugin
 * Plugin URI: https://example.com/my-custom-plugin
 * Description: A plugin to create custom page templates and import pages from CSV
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: my-custom-plugin
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('MCP_VERSION', '1.0.0');
define('MCP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MCP_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Register custom page templates
 * 
 * This function adds custom page templates to the page template dropdown
 * in the WordPress admin area when editing pages.
 */
function mcp_register_page_templates($templates) {
    // Add your custom template to the array
    // Key: template file path relative to plugin directory
    // Value: template name shown in admin dropdown
    $templates['templates/page-city-template.php'] = __('City Page Template', 'my-custom-plugin');
    
    return $templates;
}
add_filter('theme_page_templates', 'mcp_register_page_templates');

/**
 * Load custom page template from plugin
 * 
 * This function intercepts the template loading process and loads
 * our custom template from the plugin directory instead of the theme.
 */
function mcp_load_page_template($template) {
    // Get the current page ID
    $page_id = get_queried_object_id();
    
    // Get the template slug assigned to this page
    $page_template = get_post_meta($page_id, '_wp_page_template', true);
    
    // Check if our custom template is assigned to this page
    if ($page_template === 'templates/page-city-template.php') {
        // Build the full path to our template file
        $plugin_template = MCP_PLUGIN_DIR . $page_template;
        
        // Check if the template file exists
        if (file_exists($plugin_template)) {
            return $plugin_template;
        }
    }
    
    // Return the default template if ours isn't being used
    return $template;
}
add_filter('template_include', 'mcp_load_page_template');

/**
 * Enqueue styles and scripts for our custom templates
 */
function mcp_enqueue_template_assets() {
    // Only load on pages using our custom template
    if (is_page() && get_post_meta(get_the_ID(), '_wp_page_template', true) === 'templates/page-city-template.php') {
        // Enqueue custom CSS for city template
        wp_enqueue_style(
            'mcp-city-template-style',
            MCP_PLUGIN_URL . 'assets/css/city-template.css',
            array(),
            MCP_VERSION
        );
        
        // Enqueue custom JavaScript for city template
        wp_enqueue_script(
            'mcp-city-template-script',
            MCP_PLUGIN_URL . 'assets/js/city-template.js',
            array('jquery'),
            MCP_VERSION,
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'mcp_enqueue_template_assets');

// Include CSV importer functionality
require_once MCP_PLUGIN_DIR . 'includes/csv-importer.php';
```

#### Step 3: Create the Custom Page Template

Create `templates/page-city-template.php`:

```php
<?php
/**
 * Template Name: City Page Template
 * Template Post Type: page
 * Description: A custom template for displaying city information
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main city-page-template">
        
        <?php while (have_posts()) : the_post(); ?>
            
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                
                <header class="entry-header">
                    <h1 class="entry-title"><?php the_title(); ?></h1>
                    
                    <?php
                    // Display custom city metadata
                    $city_name = get_post_meta(get_the_ID(), 'city_name', true);
                    $state = get_post_meta(get_the_ID(), 'state', true);
                    $population = get_post_meta(get_the_ID(), 'population', true);
                    $country = get_post_meta(get_the_ID(), 'country', true);
                    ?>
                    
                    <?php if ($city_name): ?>
                        <div class="city-metadata">
                            <h2>City Information</h2>
                            <ul class="city-details">
                                <li><strong>City:</strong> <?php echo esc_html($city_name); ?></li>
                                <?php if ($state): ?>
                                    <li><strong>State:</strong> <?php echo esc_html($state); ?></li>
                                <?php endif; ?>
                                <?php if ($country): ?>
                                    <li><strong>Country:</strong> <?php echo esc_html($country); ?></li>
                                <?php endif; ?>
                                <?php if ($population): ?>
                                    <li><strong>Population:</strong> <?php echo esc_html(number_format($population)); ?></li>
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
                    wp_link_pages(array(
                        'before' => '<div class="page-links">' . __('Pages:', 'my-custom-plugin'),
                        'after'  => '</div>',
                    ));
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
            if (comments_open() || get_comments_number()) :
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

### Template Hierarchy

WordPress follows a template hierarchy to determine which template file to use:

1. **Custom Template** (if assigned) - Like our `page-city-template.php`
2. **page-{slug}.php** - Template for specific page slug
3. **page-{id}.php** - Template for specific page ID
4. **page.php** - General page template
5. **singular.php** - Generic template for single pages/posts
6. **index.php** - Fallback template

## Creating Pages from CSV Data

### Understanding CSV Import in WordPress

CSV (Comma-Separated Values) files are a common format for storing tabular data. WordPress doesn't have built-in CSV import for pages, so we'll create custom functionality.

### Step-by-Step CSV Import Implementation

#### Step 1: CSV File Structure

Create a sample CSV file (`cities.csv`) with the following structure:

```csv
city,state,country,population,description
New York,New York,USA,8336817,"The largest city in the United States, known for its significant impact on commerce, finance, media, art, fashion, research, technology, education, and entertainment."
Los Angeles,California,USA,3979576,"Known for its Mediterranean climate, ethnic and cultural diversity, Hollywood entertainment industry, and sprawling metropolitan area."
Chicago,Illinois,USA,2693976,"The third-most populous city in the United States, known for its bold architecture, museums, and vibrant arts scene."
Houston,Texas,USA,2320268,"The most populous city in Texas and the fourth-most populous city in the United States, known for its energy industry and NASA's Johnson Space Center."
Phoenix,Arizona,USA,1680992,"The capital and most populous city of Arizona, known for its year-round sun and warm temperatures."
```

#### Step 2: CSV Importer Class

Create `includes/csv-importer.php`:

```php
<?php
/**
 * CSV Importer functionality
 * 
 * This file handles importing city data from CSV files and creating
 * WordPress pages with custom templates.
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add admin menu for CSV importer
 */
function mcp_add_csv_importer_menu() {
    add_menu_page(
        __('CSV Importer', 'my-custom-plugin'),      // Page title
        __('CSV Importer', 'my-custom-plugin'),      // Menu title
        'manage_options',                             // Capability required
        'mcp-csv-importer',                          // Menu slug
        'mcp_csv_importer_page',                     // Callback function
        'dashicons-upload',                           // Icon
        30                                            // Position
    );
}
add_action('admin_menu', 'mcp_add_csv_importer_menu');

/**
 * Display the CSV importer admin page
 */
function mcp_csv_importer_page() {
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.', 'my-custom-plugin'));
    }
    
    // Handle form submission
    if (isset($_POST['mcp_csv_import_submit']) && check_admin_referer('mcp_csv_import_action', 'mcp_csv_import_nonce')) {
        mcp_process_csv_import();
    }
    
    // Display the form
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__('Import Cities from CSV', 'my-custom-plugin'); ?></h1>
        
        <div class="card">
            <h2><?php echo esc_html__('CSV File Format', 'my-custom-plugin'); ?></h2>
            <p><?php echo esc_html__('Your CSV file should have the following columns:', 'my-custom-plugin'); ?></p>
            <ul>
                <li><strong>city</strong> - The city name (required)</li>
                <li><strong>state</strong> - The state name (optional)</li>
                <li><strong>country</strong> - The country name (optional)</li>
                <li><strong>population</strong> - The population number (optional)</li>
                <li><strong>description</strong> - A description of the city (optional)</li>
            </ul>
            <p><strong><?php echo esc_html__('Note:', 'my-custom-plugin'); ?></strong> 
            <?php echo esc_html__('The first row should contain column headers.', 'my-custom-plugin'); ?></p>
        </div>
        
        <form method="post" enctype="multipart/form-data" class="mcp-import-form">
            <?php wp_nonce_field('mcp_csv_import_action', 'mcp_csv_import_nonce'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="csv_file"><?php echo esc_html__('CSV File', 'my-custom-plugin'); ?></label>
                    </th>
                    <td>
                        <input type="file" name="csv_file" id="csv_file" accept=".csv" required>
                        <p class="description">
                            <?php echo esc_html__('Select a CSV file to import. Maximum file size: 2MB', 'my-custom-plugin'); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="post_status"><?php echo esc_html__('Page Status', 'my-custom-plugin'); ?></label>
                    </th>
                    <td>
                        <select name="post_status" id="post_status">
                            <option value="draft"><?php echo esc_html__('Draft', 'my-custom-plugin'); ?></option>
                            <option value="publish"><?php echo esc_html__('Published', 'my-custom-plugin'); ?></option>
                            <option value="private"><?php echo esc_html__('Private', 'my-custom-plugin'); ?></option>
                        </select>
                        <p class="description">
                            <?php echo esc_html__('Select the status for imported pages', 'my-custom-plugin'); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="parent_page"><?php echo esc_html__('Parent Page (Optional)', 'my-custom-plugin'); ?></label>
                    </th>
                    <td>
                        <?php
                        wp_dropdown_pages(array(
                            'name' => 'parent_page',
                            'id' => 'parent_page',
                            'show_option_none' => __('None (Top Level)', 'my-custom-plugin'),
                            'option_none_value' => '0',
                        ));
                        ?>
                        <p class="description">
                            <?php echo esc_html__('Optionally set a parent page for all imported pages', 'my-custom-plugin'); ?>
                        </p>
                    </td>
                </tr>
            </table>
            
            <?php submit_button(__('Import Cities', 'my-custom-plugin'), 'primary', 'mcp_csv_import_submit'); ?>
        </form>
    </div>
    <?php
}

/**
 * Process the CSV file and create pages
 */
function mcp_process_csv_import() {
    // Verify file was uploaded
    if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
        add_settings_error(
            'mcp_csv_import',
            'file_upload_error',
            __('Error uploading file. Please try again.', 'my-custom-plugin'),
            'error'
        );
        return;
    }
    
    // Verify file type
    $file_info = pathinfo($_FILES['csv_file']['name']);
    if (strtolower($file_info['extension']) !== 'csv') {
        add_settings_error(
            'mcp_csv_import',
            'invalid_file_type',
            __('Please upload a valid CSV file.', 'my-custom-plugin'),
            'error'
        );
        return;
    }
    
    // Get form data
    $post_status = isset($_POST['post_status']) ? sanitize_text_field($_POST['post_status']) : 'draft';
    $parent_page = isset($_POST['parent_page']) ? absint($_POST['parent_page']) : 0;
    
    // Read and parse CSV file
    $csv_file = $_FILES['csv_file']['tmp_name'];
    $cities_data = mcp_parse_csv_file($csv_file);
    
    if (empty($cities_data)) {
        add_settings_error(
            'mcp_csv_import',
            'empty_csv',
            __('The CSV file is empty or could not be parsed.', 'my-custom-plugin'),
            'error'
        );
        return;
    }
    
    // Create pages from CSV data
    $created_pages = 0;
    $errors = array();
    
    foreach ($cities_data as $index => $city_data) {
        $result = mcp_create_city_page($city_data, $post_status, $parent_page);
        
        if (is_wp_error($result)) {
            $errors[] = sprintf(
                __('Row %d: %s', 'my-custom-plugin'),
                $index + 2, // +2 because row 1 is headers and array is 0-indexed
                $result->get_error_message()
            );
        } else {
            $created_pages++;
        }
    }
    
    // Display success/error messages
    if ($created_pages > 0) {
        add_settings_error(
            'mcp_csv_import',
            'import_success',
            sprintf(
                _n(
                    '%d page created successfully!',
                    '%d pages created successfully!',
                    $created_pages,
                    'my-custom-plugin'
                ),
                $created_pages
            ),
            'success'
        );
    }
    
    if (!empty($errors)) {
        add_settings_error(
            'mcp_csv_import',
            'import_errors',
            __('The following errors occurred:', 'my-custom-plugin') . '<br>' . implode('<br>', $errors),
            'error'
        );
    }
    
    settings_errors('mcp_csv_import');
}

/**
 * Parse CSV file and return array of data
 * 
 * @param string $file_path Path to the CSV file
 * @return array Array of parsed CSV data
 */
function mcp_parse_csv_file($file_path) {
    $data = array();
    
    // Open the CSV file for reading
    $handle = fopen($file_path, 'r');
    
    if ($handle === false) {
        return $data;
    }
    
    // Read the first row to get column headers
    $headers = fgetcsv($handle);
    
    if ($headers === false) {
        fclose($handle);
        return $data;
    }
    
    // Normalize headers (trim whitespace, convert to lowercase)
    $headers = array_map(function($header) {
        return strtolower(trim($header));
    }, $headers);
    
    // Read each subsequent row
    while (($row = fgetcsv($handle)) !== false) {
        // Skip empty rows
        if (empty(array_filter($row))) {
            continue;
        }
        
        // Combine headers with row data to create associative array
        $row_data = array();
        foreach ($headers as $index => $header) {
            $row_data[$header] = isset($row[$index]) ? trim($row[$index]) : '';
        }
        
        $data[] = $row_data;
    }
    
    // Close the file handle
    fclose($handle);
    
    return $data;
}

/**
 * Create a page for a city from CSV data
 * 
 * @param array $city_data City data from CSV row
 * @param string $post_status Status for the new page
 * @param int $parent_page Parent page ID
 * @return int|WP_Error Page ID on success, WP_Error on failure
 */
function mcp_create_city_page($city_data, $post_status = 'draft', $parent_page = 0) {
    // Validate required data
    if (empty($city_data['city'])) {
        return new WP_Error('missing_city', __('City name is required.', 'my-custom-plugin'));
    }
    
    $city_name = sanitize_text_field($city_data['city']);
    
    // Check if a page with this title already exists
    $existing_page = get_page_by_title($city_name, OBJECT, 'page');
    if ($existing_page) {
        return new WP_Error(
            'page_exists',
            sprintf(__('A page for "%s" already exists.', 'my-custom-plugin'), $city_name)
        );
    }
    
    // Prepare page content
    $content = isset($city_data['description']) ? $city_data['description'] : '';
    
    // Create the page
    $page_data = array(
        'post_title'    => $city_name,
        'post_content'  => wp_kses_post($content),
        'post_status'   => $post_status,
        'post_type'     => 'page',
        'post_author'   => get_current_user_id(),
        'post_parent'   => $parent_page,
    );
    
    // Insert the page
    $page_id = wp_insert_post($page_data);
    
    if (is_wp_error($page_id)) {
        return $page_id;
    }
    
    // Assign the custom page template
    update_post_meta($page_id, '_wp_page_template', 'templates/page-city-template.php');
    
    // Add custom metadata
    update_post_meta($page_id, 'city_name', sanitize_text_field($city_name));
    
    if (!empty($city_data['state'])) {
        update_post_meta($page_id, 'state', sanitize_text_field($city_data['state']));
    }
    
    if (!empty($city_data['country'])) {
        update_post_meta($page_id, 'country', sanitize_text_field($city_data['country']));
    }
    
    if (!empty($city_data['population'])) {
        // Remove any commas and convert to integer
        $population = absint(str_replace(',', '', $city_data['population']));
        update_post_meta($page_id, 'population', $population);
    }
    
    return $page_id;
}

/**
 * Alternative: Process CSV via WP-CLI command
 * 
 * Usage: wp csv-import cities path/to/cities.csv --status=publish
 */
if (defined('WP_CLI') && WP_CLI) {
    /**
     * Import cities from CSV file
     *
     * ## OPTIONS
     *
     * <file>
     * : Path to the CSV file
     *
     * [--status=<status>]
     * : Post status for created pages
     * ---
     * default: draft
     * options:
     *   - draft
     *   - publish
     *   - private
     * ---
     *
     * [--parent=<parent>]
     * : Parent page ID
     * ---
     * default: 0
     * ---
     *
     * ## EXAMPLES
     *
     *     wp csv-import cities cities.csv --status=publish
     *     wp csv-import cities /path/to/cities.csv --status=draft --parent=42
     */
    WP_CLI::add_command('csv-import cities', function($args, $assoc_args) {
        $file = $args[0];
        
        if (!file_exists($file)) {
            WP_CLI::error("File not found: $file");
        }
        
        $status = isset($assoc_args['status']) ? $assoc_args['status'] : 'draft';
        $parent = isset($assoc_args['parent']) ? absint($assoc_args['parent']) : 0;
        
        $cities_data = mcp_parse_csv_file($file);
        
        if (empty($cities_data)) {
            WP_CLI::error('Could not parse CSV file or file is empty.');
        }
        
        $progress = \WP_CLI\Utils\make_progress_bar('Creating pages', count($cities_data));
        
        $created = 0;
        $errors = 0;
        
        foreach ($cities_data as $city_data) {
            $result = mcp_create_city_page($city_data, $status, $parent);
            
            if (is_wp_error($result)) {
                WP_CLI::warning($result->get_error_message());
                $errors++;
            } else {
                $created++;
            }
            
            $progress->tick();
        }
        
        $progress->finish();
        
        WP_CLI::success("Created $created pages. $errors errors.");
    });
}
```

### Understanding the Code

Let's break down the key components:

#### 1. File Upload Handling

```php
// Verify file upload
if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
    // Handle error
}
```

This checks if a file was uploaded successfully. The `$_FILES` superglobal contains information about uploaded files.

#### 2. CSV Parsing

```php
function mcp_parse_csv_file($file_path) {
    $handle = fopen($file_path, 'r');  // Open file for reading
    $headers = fgetcsv($handle);        // Read first row as headers
    
    while (($row = fgetcsv($handle)) !== false) {
        // Process each row
    }
    
    fclose($handle);  // Close file handle
}
```

The `fgetcsv()` function reads a line from a CSV file and parses it into an array.

#### 3. Creating Pages

```php
wp_insert_post(array(
    'post_title'    => $city_name,
    'post_content'  => $content,
    'post_status'   => 'publish',
    'post_type'     => 'page',
));
```

The `wp_insert_post()` function creates a new page in WordPress with the specified parameters.

#### 4. Adding Metadata

```php
update_post_meta($page_id, 'city_name', $city_name);
update_post_meta($page_id, 'population', $population);
```

`update_post_meta()` stores custom data associated with a page. This data can be retrieved later using `get_post_meta()`.

#### 5. Assigning Templates

```php
update_post_meta($page_id, '_wp_page_template', 'templates/page-city-template.php');
```

This special meta key tells WordPress which custom template to use for the page.

## Complete Working Example

### Full Plugin Structure

Here's a complete directory structure for the working plugin:

```
my-custom-plugin/
├── my-custom-plugin.php
├── includes/
│   └── csv-importer.php
├── templates/
│   └── page-city-template.php
├── assets/
│   ├── css/
│   │   └── city-template.css
│   └── js/
│       └── city-template.js
└── sample-data/
    └── cities.csv
```

### Additional Files

#### assets/css/city-template.css

```css
.city-page-template {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.city-metadata {
    background: #f5f5f5;
    border-left: 4px solid #0073aa;
    padding: 20px;
    margin: 20px 0;
}

.city-metadata h2 {
    margin-top: 0;
    color: #0073aa;
}

.city-details {
    list-style: none;
    padding: 0;
}

.city-details li {
    padding: 8px 0;
    border-bottom: 1px solid #ddd;
}

.city-details li:last-child {
    border-bottom: none;
}

.city-details strong {
    display: inline-block;
    min-width: 120px;
    color: #333;
}
```

#### assets/js/city-template.js

```javascript
jQuery(document).ready(function($) {
    // Add interactive features to city pages
    
    // Example: Animate city information on page load
    $('.city-metadata').hide().fadeIn(1000);
    
    // Example: Add click handler for city details
    $('.city-details li').on('click', function() {
        $(this).toggleClass('highlighted');
    });
    
    // You can add more interactive features here
    // Such as: maps integration, weather data, etc.
});
```

### Step-by-Step Usage Guide

#### Step 1: Install the Plugin

1. Upload the `my-custom-plugin` folder to `/wp-content/plugins/`
2. Activate the plugin through the WordPress admin panel

#### Step 2: Prepare Your CSV File

Create a CSV file with city data:

```csv
city,state,country,population,description
San Francisco,California,USA,873965,"Famous for the Golden Gate Bridge, cable cars, and vibrant tech industry."
Seattle,Washington,USA,753675,"Known for its coffee culture, tech companies, and the iconic Space Needle."
Boston,Massachusetts,USA,692600,"Rich in history, home to prestigious universities and the Freedom Trail."
```

#### Step 3: Import the Data

1. Go to **CSV Importer** in the WordPress admin menu
2. Select your CSV file
3. Choose the page status (Draft or Published)
4. Optionally select a parent page
5. Click **Import Cities**

#### Step 4: View Your Pages

Navigate to **Pages** → **All Pages** to see your newly created city pages. Each page will automatically use the custom city template.

### Advanced Customization

#### Adding More Custom Fields

To add more fields to your city pages, modify the CSV file and update the import function:

```php
// In mcp_create_city_page function, add:
if (!empty($city_data['timezone'])) {
    update_post_meta($page_id, 'timezone', sanitize_text_field($city_data['timezone']));
}

if (!empty($city_data['founded_year'])) {
    update_post_meta($page_id, 'founded_year', absint($city_data['founded_year']));
}
```

Then update your template to display these fields:

```php
$timezone = get_post_meta(get_the_ID(), 'timezone', true);
$founded_year = get_post_meta(get_the_ID(), 'founded_year', true);

if ($timezone) {
    echo '<li><strong>Timezone:</strong> ' . esc_html($timezone) . '</li>';
}
```

#### Bulk Operations

To update existing pages or perform bulk operations, you can create additional admin pages:

```php
function mcp_bulk_update_pages() {
    $args = array(
        'post_type' => 'page',
        'meta_key' => '_wp_page_template',
        'meta_value' => 'templates/page-city-template.php',
        'posts_per_page' => -1,
    );
    
    $city_pages = get_posts($args);
    
    foreach ($city_pages as $page) {
        // Perform bulk update operations
        // For example: update meta data, regenerate content, etc.
    }
}
```

## Resources

### Official WordPress Documentation

- [Plugin Development Handbook](https://developer.wordpress.org/plugins/) - Complete guide to WordPress plugin development
- [Theme Development Handbook](https://developer.wordpress.org/themes/) - Guide to WordPress theme development including templates
- [Page Templates](https://developer.wordpress.org/themes/template-files-section/page-template-files/) - Official documentation on page templates
- [Template Hierarchy](https://developer.wordpress.org/themes/basics/template-hierarchy/) - Understanding how WordPress selects templates
- [Custom Post Meta](https://developer.wordpress.org/plugins/metadata/custom-meta-boxes/) - Working with custom metadata

### File Handling in WordPress

- [Filesystem API](https://developer.wordpress.org/plugins/filesystem-api/) - WordPress file handling functions
- [File Upload Security](https://developer.wordpress.org/apis/security/data-validation/) - Best practices for handling file uploads
- [CSV Import/Export](https://developer.wordpress.org/reference/functions/fgetcsv/) - PHP CSV handling functions

### WordPress Functions Reference

- [wp_insert_post()](https://developer.wordpress.org/reference/functions/wp_insert_post/) - Creating posts and pages programmatically
- [get_post_meta()](https://developer.wordpress.org/reference/functions/get_post_meta/) - Retrieving custom metadata
- [update_post_meta()](https://developer.wordpress.org/reference/functions/update_post_meta/) - Saving custom metadata
- [wp_kses_post()](https://developer.wordpress.org/reference/functions/wp_kses_post/) - Sanitizing content for posts
- [sanitize_text_field()](https://developer.wordpress.org/reference/functions/sanitize_text_field/) - Sanitizing text input

### WordPress Coding Standards

- [PHP Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/) - WordPress PHP coding standards
- [JavaScript Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/javascript/) - WordPress JavaScript coding standards
- [CSS Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/css/) - WordPress CSS coding standards

### Security Best Practices

- [Data Validation](https://developer.wordpress.org/apis/security/data-validation/) - Validating and sanitizing user input
- [Escaping Output](https://developer.wordpress.org/apis/security/escaping/) - Preventing XSS attacks
- [Nonces](https://developer.wordpress.org/apis/security/nonces/) - Protecting forms from CSRF attacks
- [Plugin Security](https://developer.wordpress.org/plugins/security/) - Security best practices for plugins

### Community Resources

- [WordPress Stack Exchange](https://wordpress.stackexchange.com/) - Q&A community for WordPress development
- [WordPress.tv](https://wordpress.tv/) - Video tutorials and WordCamp presentations
- [WPBeginner](https://www.wpbeginner.com/) - WordPress tutorials for beginners
- [Tom McFarlin's Blog](https://tommcfarlin.com/) - Advanced WordPress development topics
- [Carl Alexander's Blog](https://carlalexander.ca/) - WordPress architecture and best practices

### Development Tools

- [Local by Flywheel](https://localwp.com/) - Local WordPress development environment
- [Query Monitor](https://wordpress.org/plugins/query-monitor/) - Debugging plugin for WordPress
- [WP-CLI](https://wp-cli.org/) - Command-line interface for WordPress
- [Debug Bar](https://wordpress.org/plugins/debug-bar/) - Adds debugging menu to the admin bar
- [GitHub](https://github.com/) - Version control and collaboration