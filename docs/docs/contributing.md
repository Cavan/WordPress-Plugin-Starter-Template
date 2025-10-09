---
sidebar_position: 14
---

# Contributing

Thank you for your interest in contributing to the WordPress Plugin Starter Template!

## How to Contribute

### 1. Fork the Repository

Fork the repository on GitHub to your account.

```bash
# Clone your fork
git clone https://github.com/YOUR-USERNAME/WordPress-Plugin-Starter-Template.git
cd WordPress-Plugin-Starter-Template
```

### 2. Create a Feature Branch

Create a new branch for your feature or bug fix:

```bash
git checkout -b feature/amazing-feature
# or
git checkout -b fix/bug-description
```

### 3. Make Your Changes

Make your changes following the guidelines below.

### 4. Test Your Changes

Test thoroughly before submitting:

- Test on a fresh WordPress installation
- Check for PHP errors and warnings
- Test with different themes
- Verify HTML validation
- Run WordPress coding standards checker

### 5. Commit Your Changes

Write clear, descriptive commit messages:

```bash
git add .
git commit -m 'Add some amazing feature'
```

**Commit message format:**
```
Short summary (50 characters or less)

More detailed explanation if needed. Wrap at 72 characters.
Include motivation for the change and contrast with previous behavior.

- Bullet points are okay
- Use present tense ("Add feature" not "Added feature")
- Reference issues: Fixes #123, Closes #456
```

### 6. Push to Your Branch

```bash
git push origin feature/amazing-feature
```

### 7. Open a Pull Request

- Go to the original repository on GitHub
- Click "New Pull Request"
- Select your branch
- Provide a clear description of your changes
- Reference any related issues

## Guidelines

### Code Standards

#### WordPress Coding Standards

Follow [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/):

- **PHP**: Follow WordPress PHP standards
- **JavaScript**: Follow WordPress JavaScript standards
- **CSS**: Follow WordPress CSS standards
- **HTML**: Use semantic, accessible HTML

#### Code Style

```php
// Good: WordPress style
function wp_plugin_starter_my_function( $param ) {
    if ( $param ) {
        return true;
    }
    return false;
}

// Good: Proper spacing
$array = array( 'key' => 'value' );

// Good: String concatenation
$output = 'Hello ' . $name . '!';
```

#### Naming Conventions

- **Functions**: `wp_plugin_starter_function_name()`
- **Classes**: `WP_Plugin_Starter_Class_Name`
- **Constants**: `WP_PLUGIN_STARTER_CONSTANT`
- **Variables**: `$descriptive_variable_name`

#### Documentation

Add PHPDoc blocks for all functions and classes:

```php
/**
 * Short description of function
 *
 * Longer description if needed, explaining the purpose,
 * usage, and any important notes.
 *
 * @since 1.0.0
 * @param string $param1 Description of parameter.
 * @param array  $param2 Description of parameter.
 * @return bool True on success, false on failure.
 */
function wp_plugin_starter_example( $param1, $param2 = array() ) {
    // Function code
}
```

### Testing

#### Before Submitting

- [ ] Test on fresh WordPress installation
- [ ] Check for PHP errors (with `WP_DEBUG` enabled)
- [ ] Test with different themes
- [ ] Verify browser console has no errors
- [ ] Run PHPCS: `./vendor/bin/phpcs --standard=WordPress .`
- [ ] Test on PHP 7.4, 8.0, and 8.2
- [ ] Verify responsive design (if UI changes)

#### Writing Tests

If adding new functionality, include tests:

```php
// tests/test-new-feature.php
class NewFeatureTest extends WP_UnitTestCase {
    
    public function test_new_feature() {
        $result = wp_plugin_starter_new_feature();
        $this->assertTrue( $result );
    }
}
```

### Documentation

#### Update Documentation

When adding features, update relevant documentation:

- **README.md**: Update feature list or usage instructions
- **docs/**: Update or create documentation pages
- **CODE-EXAMPLES.md**: Add practical examples if applicable
- **CHANGELOG.md**: Add entry for your changes

#### Documentation Style

- Use clear, concise language
- Include code examples
- Add screenshots for UI changes
- Keep formatting consistent

Example:

```markdown
## New Feature

Brief description of the feature.

### Usage

```php
// Code example
wp_plugin_starter_new_feature( $param );
\`\`\`

### Parameters

- `$param` (string) - Description of parameter
```

### Pull Requests

#### Pull Request Checklist

- [ ] Code follows WordPress coding standards
- [ ] All tests pass
- [ ] Documentation is updated
- [ ] Commit messages are clear
- [ ] PR description explains the changes
- [ ] Related issues are referenced

#### PR Description Template

```markdown
## Description

Brief description of changes

## Type of Change

- [ ] Bug fix
- [ ] New feature
- [ ] Documentation update
- [ ] Code refactoring

## Testing

- [ ] Tested on fresh WordPress installation
- [ ] No PHP errors or warnings
- [ ] Works with default theme
- [ ] Coding standards check passed

## Related Issues

Fixes #123
Closes #456

## Screenshots (if applicable)

Add screenshots here
```

#### Review Process

- Maintainers will review your PR
- Be responsive to feedback
- Make requested changes promptly
- Discussion is encouraged!

## Reporting Issues

### Before Reporting

- Search existing issues first
- Check if it's already fixed in the latest version
- Verify it's not a theme/plugin conflict

### Issue Template

```markdown
**Describe the issue**
A clear description of what the issue is.

**To Reproduce**
1. Go to '...'
2. Click on '...'
3. See error

**Expected behavior**
What you expected to happen.

**Environment**
- WordPress version:
- PHP version:
- Theme:
- Browser (if relevant):

**Additional context**
Any other relevant information.
```

## Types of Contributions

### Code Contributions

- Bug fixes
- New features
- Performance improvements
- Code refactoring
- Security enhancements

### Documentation

- Fix typos or errors
- Improve clarity
- Add examples
- Translate documentation

### Testing

- Report bugs
- Test new features
- Provide feedback
- Beta testing

### Design

- UI improvements
- Icon design
- Screenshot creation
- User experience enhancements

## Code Review Process

1. **Automated Checks**: PHPCS and tests run automatically
2. **Maintainer Review**: Code is reviewed by maintainers
3. **Feedback**: Comments and suggestions provided
4. **Revisions**: Make requested changes
5. **Approval**: PR is approved
6. **Merge**: Changes are merged into main branch

## Community

### Communication

- Be respectful and professional
- Provide constructive feedback
- Help others when possible
- Follow the [WordPress Code of Conduct](https://make.wordpress.org/handbook/community-code-of-conduct/)

### Getting Help

- Open an issue for questions
- Check existing documentation
- Ask in WordPress Stack Exchange
- Join WordPress Slack

## Recognition

Contributors will be:

- Listed in CHANGELOG.md
- Credited in commits
- Mentioned in release notes
- Added to contributors list

## Development Setup

### Prerequisites

```bash
# PHP 7.4 or higher
php --version

# Composer
composer --version

# Node.js and npm (for docs)
node --version
npm --version
```

### Setup

```bash
# Install dependencies
composer install

# Install PHPCS standards
./vendor/bin/phpcs --config-set installed_paths vendor/wp-coding-standards/wpcs

# Setup documentation
cd docs
npm install
npm start
```

## Questions?

Feel free to open an issue for any questions or suggestions!

Thank you for contributing! 🎉

## Related Documentation

- [Testing](testing) - Testing guidelines
- [Resources](resources) - Development resources
- [Code Examples](code-examples) - Example implementations
