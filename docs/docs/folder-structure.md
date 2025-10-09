---
sidebar_position: 3
---

# Folder Structure

Understanding the plugin's folder structure is key to working efficiently with the template.

## Overview

```
wp-plugin-starter/
├── admin/                  # Admin-specific functionality
│   ├── css/               # Admin stylesheets
│   ├── js/                # Admin JavaScript
│   ├── partials/          # Admin view templates
│   └── class-admin.php    # Admin class
├── public/                # Public-facing functionality
│   ├── css/               # Public stylesheets
│   ├── js/                # Public JavaScript
│   └── class-public.php   # Public class
├── includes/              # Shared/core functionality
│   ├── class-activator.php    # Activation hooks
│   ├── class-deactivator.php  # Deactivation hooks
│   ├── class-loader.php       # Hook loader
│   └── class-wp-plugin-starter.php  # Main plugin class
├── assets/                # Additional assets
│   └── images/           # Image files
├── languages/             # Translation files
├── data/                  # Sample data files
├── docs/                  # Documentation (Docusaurus)
├── CODE-EXAMPLES.md       # Code examples documentation
├── README.md              # Project readme
└── wp-plugin-starter.php  # Main plugin file
```

## Directory Details

### `/admin`

Contains all admin-facing functionality:

- **`class-admin.php`**: Main admin class that handles admin menu, settings pages, and admin-specific hooks
- **`css/`**: Admin-specific stylesheets
- **`js/`**: Admin-specific JavaScript files
- **`partials/`**: Template files for admin pages

### `/public`

Contains all public-facing functionality:

- **`class-public.php`**: Main public class that handles frontend display and public-specific hooks
- **`css/`**: Public-facing stylesheets
- **`js/`**: Public-facing JavaScript files

### `/includes`

Contains core plugin functionality:

- **`class-wp-plugin-starter.php`**: Main plugin class that orchestrates the plugin
- **`class-loader.php`**: Registers all hooks with WordPress
- **`class-activator.php`**: Handles plugin activation
- **`class-deactivator.php`**: Handles plugin deactivation

### `/assets`

Static assets like images, fonts, or other media files used by the plugin.

### `/languages`

Translation files for internationalization (`.pot`, `.po`, `.mo` files).

### `/data`

Sample data files for testing code examples (CSV, JSON, TXT files).

### `/docs`

Documentation built with Docusaurus (this site!).

## Main Plugin File

**`wp-plugin-starter.php`**: The main plugin file that WordPress reads to load the plugin. Contains:

- Plugin header information
- Version constants
- Activation/deactivation hooks
- Plugin initialization

## Best Practices

### Organizing Your Code

1. **Admin code** goes in `/admin`
2. **Public code** goes in `/public`
3. **Shared code** goes in `/includes`
4. **Keep classes focused** - one responsibility per class
5. **Use the loader** to register hooks centrally

### Asset Management

- Enqueue styles and scripts properly using WordPress functions
- Load admin assets only in admin context
- Load public assets only when needed

### File Naming

- Use lowercase with hyphens for file names: `class-my-feature.php`
- Prefix class names: `WP_Plugin_Starter_My_Feature`
- Prefix functions: `wp_plugin_starter_my_function()`
