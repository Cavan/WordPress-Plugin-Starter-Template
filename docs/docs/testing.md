---
sidebar_position: 13
---

# Testing

Guidelines and best practices for testing your WordPress plugin.

## Before Deploying

Before deploying your plugin, ensure you've completed these essential tests:

### 1. Fresh Installation Test

Test on a clean WordPress installation:

```bash
# Using WP-CLI to create a test site
wp core download
wp config create --dbname=testdb --dbuser=root --dbpass=password
wp core install --url=test.local --title="Test Site" --admin_user=admin --admin_password=password --admin_email=admin@test.local
```

### 2. Theme Compatibility

Test with different themes:

- **Default WordPress Themes**: Twenty Twenty-Four, Twenty Twenty-Three
- **Popular Themes**: Astra, GeneratePress, OceanWP
- **Block Themes**: Modern WordPress block-based themes

### 3. Error Checking

Enable WordPress debugging in `wp-config.php`:

```php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
define( 'SCRIPT_DEBUG', true );
```

Check for:
- PHP errors and warnings
- JavaScript console errors
- Deprecated function notices

### 4. HTML Validation

Validate your HTML output:

- Use [W3C Markup Validation Service](https://validator.w3.org/)
- Check for properly closed tags
- Ensure semantic HTML structure
- Verify accessibility attributes

### 5. JavaScript Testing

Test with browser console open:

```javascript
// Check for JavaScript errors
// Monitor network requests
// Verify AJAX responses
```

Test in different browsers:
- Chrome/Edge
- Firefox
- Safari
- Mobile browsers

### 6. PHP Version Compatibility

Test on different PHP versions:

- PHP 7.4 (minimum supported)
- PHP 8.0
- PHP 8.1
- PHP 8.2

Using Docker for PHP version testing:

```bash
# PHP 7.4
docker run --rm -v "$PWD":/app -w /app php:7.4-cli php -l your-file.php

# PHP 8.0
docker run --rm -v "$PWD":/app -w /app php:8.0-cli php -l your-file.php

# PHP 8.2
docker run --rm -v "$PWD":/app -w /app php:8.2-cli php -l your-file.php
```

### 7. WordPress Coding Standards

Use PHP_CodeSniffer with WordPress standards:

```bash
# Install PHP_CodeSniffer
composer require --dev squizlabs/php_codesniffer

# Install WordPress Coding Standards
composer require --dev wp-coding-standards/wpcs

# Configure PHPCS
./vendor/bin/phpcs --config-set installed_paths vendor/wp-coding-standards/wpcs

# Check your code
./vendor/bin/phpcs --standard=WordPress /path/to/your/plugin
```

## Automated Testing

### PHPUnit Testing

Set up PHPUnit tests:

```bash
# Install WordPress test suite
bash bin/install-wp-tests.sh wordpress_test root '' localhost latest

# Run tests
./vendor/bin/phpunit
```

**Example test** (`tests/test-plugin.php`):

```php
<?php
class PluginTest extends WP_UnitTestCase {
    
    public function test_plugin_activated() {
        $this->assertTrue( is_plugin_active( 'wp-plugin-starter/wp-plugin-starter.php' ) );
    }
    
    public function test_option_exists() {
        $option = get_option( 'wp_plugin_starter_example' );
        $this->assertNotFalse( $option );
    }
    
    public function test_shortcode_registered() {
        $this->assertTrue( shortcode_exists( 'my_shortcode' ) );
    }
}
```

### Integration Testing

Test plugin interactions:

```php
public function test_post_creation() {
    $post_id = wp_insert_post( array(
        'post_title'   => 'Test Post',
        'post_content' => 'Test content',
        'post_status'  => 'publish',
    ) );
    
    $this->assertGreaterThan( 0, $post_id );
    $this->assertEquals( 'Test Post', get_the_title( $post_id ) );
}

public function test_meta_data() {
    $post_id = $this->factory->post->create();
    update_post_meta( $post_id, 'test_key', 'test_value' );
    
    $meta_value = get_post_meta( $post_id, 'test_key', true );
    $this->assertEquals( 'test_value', $meta_value );
}
```

## Manual Testing Checklist

### Functionality Testing

- [ ] Plugin activates without errors
- [ ] Plugin deactivates cleanly
- [ ] Settings save correctly
- [ ] Admin pages display properly
- [ ] Public features work as expected
- [ ] Shortcodes render correctly
- [ ] AJAX requests complete successfully
- [ ] Custom post types register properly
- [ ] Taxonomies work correctly

### Security Testing

- [ ] Forms include nonces
- [ ] User capabilities are checked
- [ ] Input is sanitized
- [ ] Output is escaped
- [ ] SQL queries use `$wpdb->prepare()`
- [ ] File uploads are validated
- [ ] AJAX requests verify nonces

### Performance Testing

- [ ] No slow database queries
- [ ] Assets are minified (production)
- [ ] Caching is implemented
- [ ] Large datasets use pagination
- [ ] Images are optimized
- [ ] HTTP requests are minimized

### Accessibility Testing

Use accessibility testing tools:

- [WAVE](https://wave.webaim.org/) - Web accessibility evaluation tool
- [axe DevTools](https://www.deque.com/axe/devtools/) - Browser extension
- [Lighthouse](https://developers.google.com/web/tools/lighthouse) - Built into Chrome

Check for:
- [ ] Keyboard navigation works
- [ ] Screen reader compatibility
- [ ] Proper ARIA labels
- [ ] Sufficient color contrast
- [ ] Alt text for images

### Responsive Testing

Test on different screen sizes:

- Desktop (1920px, 1366px, 1024px)
- Tablet (768px)
- Mobile (375px, 320px)

```css
/* Use browser dev tools responsive mode */
/* Or test on actual devices */
```

## Common Testing Scenarios

### Testing Activation/Deactivation

```php
// Test activation
public function test_activation() {
    activate_plugin( 'wp-plugin-starter/wp-plugin-starter.php' );
    
    // Check if options are set
    $this->assertNotFalse( get_option( 'wp_plugin_starter_version' ) );
}

// Test deactivation
public function test_deactivation() {
    deactivate_plugins( 'wp-plugin-starter/wp-plugin-starter.php' );
    
    // Verify cleanup if needed
    $this->assertTrue( is_plugin_inactive( 'wp-plugin-starter/wp-plugin-starter.php' ) );
}
```

### Testing Database Operations

```php
public function test_custom_table() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'custom_table';
    
    // Check if table exists
    $this->assertEquals( $table_name, $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) );
    
    // Test insert
    $result = $wpdb->insert( $table_name, array(
        'column1' => 'value1',
        'column2' => 'value2',
    ) );
    
    $this->assertNotFalse( $result );
}
```

### Testing AJAX

```php
public function test_ajax_request() {
    // Set up AJAX action
    $_POST['action'] = 'my_action';
    $_POST['nonce'] = wp_create_nonce( 'my_nonce' );
    
    // Capture output
    ob_start();
    try {
        $this->_handleAjax( 'my_action' );
    } catch ( WPAjaxDieContinueException $e ) {
        // Expected
    }
    $response = ob_get_clean();
    
    // Parse JSON response
    $data = json_decode( $response, true );
    
    $this->assertTrue( $data['success'] );
}
```

## Performance Monitoring

### Query Monitor Plugin

Install and use Query Monitor to track:
- Database queries
- PHP errors
- Hook callbacks
- HTTP requests
- Asset loading

### Performance Metrics

Monitor these metrics:
- Page load time
- Database query count
- Database query time
- Memory usage
- Asset size

### Using WP-CLI for Testing

```bash
# Check for deprecated functions
wp plugin verify-checksums wp-plugin-starter

# Test with different WordPress versions
wp core update --version=6.3

# Profile plugin performance
wp profile stage --all
```

## Continuous Integration

### GitHub Actions Example

Create `.github/workflows/test.yml`:

```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    
    services:
      mysql:
        image: mysql:5.7
        env:
          MYSQL_ROOT_PASSWORD: root
          MYSQL_DATABASE: wordpress_test
        ports:
          - 3306:3306
    
    steps:
      - uses: actions/checkout@v2
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.0'
      
      - name: Install dependencies
        run: composer install
      
      - name: Run PHPCS
        run: ./vendor/bin/phpcs --standard=WordPress .
      
      - name: Run PHPUnit
        run: ./vendor/bin/phpunit
```

## Documentation Testing

- [ ] README is up to date
- [ ] Code examples work
- [ ] Screenshots are current
- [ ] Installation instructions are clear
- [ ] FAQs are helpful

## Testing Tools Summary

| Tool | Purpose |
|------|---------|
| **Query Monitor** | Debug queries and performance |
| **Debug Bar** | View debug information |
| **PHPCS** | Check coding standards |
| **PHPUnit** | Automated unit testing |
| **WP-CLI** | Command-line testing |
| **Browser DevTools** | Frontend debugging |
| **Lighthouse** | Performance and accessibility |

## Best Practices

1. **Test early and often** during development
2. **Automate repetitive tests** with scripts
3. **Use version control** to track changes
4. **Document test results** and issues found
5. **Test with real data** when possible
6. **Include edge cases** in your tests
7. **Test on different hosting environments**
8. **Get feedback** from beta testers

## Pre-Release Checklist

Before releasing a new version:

- [ ] All tests pass
- [ ] Code follows WordPress standards
- [ ] Documentation is updated
- [ ] Changelog is current
- [ ] Version numbers are updated
- [ ] No debugging code left in
- [ ] Assets are optimized
- [ ] Security audit completed
- [ ] Performance tested
- [ ] Accessibility verified

## Related Documentation

- [Resources](resources) - Development tools and resources
- [Contributing](contributing) - Guidelines for contributors
