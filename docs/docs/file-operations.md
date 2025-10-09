---
sidebar_position: 6
---

# File Operations

Learn how to read and process various file types in your WordPress plugin.

## Reading a Text File

Simple text file reading from the plugin directory:

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

## Reading a CSV File

Parse CSV files with headers:

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

**Output format:**
```php
array(
    array(
        'title' => 'Page Title',
        'content' => 'Page content...',
        'meta_key' => 'custom_field',
        'meta_value' => 'value'
    ),
    // ... more rows
)
```

## Reading a JSON File

Parse JSON files and handle errors:

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

## Using WordPress Filesystem API (Recommended)

The WordPress Filesystem API is the recommended way to interact with files:

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

### Why Use WordPress Filesystem API?

- **Security**: Handles file permissions properly
- **Compatibility**: Works with different hosting environments
- **Flexibility**: Supports FTP, SSH, and direct methods
- **WordPress Standard**: Follows WordPress best practices

## Writing Files

```php
/**
 * Write content to a file using WordPress Filesystem API
 */
function wp_plugin_starter_write_file( $filename, $content ) {
    global $wp_filesystem;
    
    if ( empty( $wp_filesystem ) ) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        WP_Filesystem();
    }
    
    $file_path = WP_PLUGIN_STARTER_PATH . 'data/' . $filename;
    
    // Write content to file
    $result = $wp_filesystem->put_contents( $file_path, $content, FS_CHMOD_FILE );
    
    if ( ! $result ) {
        return new WP_Error( 'write_error', __( 'Could not write file', 'wp-plugin-starter' ) );
    }
    
    return true;
}
```

## Best Practices

### Security

1. **Validate file paths** - ensure files are in expected locations
2. **Sanitize data** from files before using it
3. **Check permissions** before reading/writing
4. **Use nonces** when handling file uploads

### Performance

1. **Cache file contents** when reading frequently
2. **Use transients** for expensive operations
3. **Process large files** in chunks
4. **Clean up** temporary files

### Error Handling

```php
$result = wp_plugin_starter_read_csv_file();

if ( is_wp_error( $result ) ) {
    error_log( 'File read error: ' . $result->get_error_message() );
    return;
}

// Process data
foreach ( $result as $row ) {
    // Your code here
}
```

## Sample Data Files

Test these examples with the sample files in `/data`:

- `example.txt` - Simple text file
- `example.csv` - CSV with page data
- `example.json` - JSON with page objects

## Related Examples

- [Data Looping](data-looping) - Process the data after reading files
- [Bulk Page Creation](bulk-page-creation) - Create pages from file data
