# Documentation Usage Guide

This guide explains how to use and contribute to the WordPress Plugin Starter Template documentation.

## Table of Contents

- [Viewing the Documentation](#viewing-the-documentation)
- [Running Locally](#running-locally)
- [Using Docker](#using-docker)
- [Building for Production](#building-for-production)
- [Contributing to Documentation](#contributing-to-documentation)
- [Documentation Structure](#documentation-structure)

## Viewing the Documentation

The documentation is built with Docusaurus and can be viewed in several ways:

### Online (GitHub Pages)

Once deployed, the documentation will be available at:
```
https://cavan.github.io/WordPress-Plugin-Starter-Template/
```

### Local Development

See [Running Locally](#running-locally) below.

## Running Locally

### Prerequisites

- Node.js 18 or higher
- npm (comes with Node.js)

### Steps

1. **Navigate to the docs directory:**
   ```bash
   cd docs
   ```

2. **Install dependencies** (first time only):
   ```bash
   npm install
   ```

3. **Start the development server:**
   ```bash
   npm start
   ```

4. **Open your browser** and visit:
   ```
   http://localhost:3000
   ```

The documentation will automatically reload when you make changes to the files.

### Stopping the Server

Press `Ctrl+C` in the terminal to stop the development server.

## Using Docker

Docker provides an isolated environment and requires no local Node.js installation.

### Prerequisites

- Docker
- Docker Compose

### Steps

1. **Navigate to the docs directory:**
   ```bash
   cd docs
   ```

2. **Start the Docker container:**
   ```bash
   docker-compose up
   ```

3. **Open your browser** and visit:
   ```
   http://localhost:3000
   ```

### Running in Background

To run the container in detached mode:
```bash
docker-compose up -d
```

View logs:
```bash
docker-compose logs -f
```

### Stopping Docker

Stop the container:
```bash
docker-compose down
```

### Rebuilding Docker Image

If you update dependencies or Docker configuration:
```bash
docker-compose down
docker-compose build --no-cache
docker-compose up
```

## Building for Production

### Build Static Files

Generate optimized static files for deployment:

```bash
cd docs
npm run build
```

This creates a `build/` directory with static HTML, CSS, and JavaScript files.

### Test Production Build Locally

Serve the production build locally to test:

```bash
npm run serve
```

Visit `http://localhost:3000` to preview the production build.

## Contributing to Documentation

### Adding a New Page

1. **Create a markdown file** in `docs/docs/`:
   ```bash
   touch docs/docs/my-new-page.md
   ```

2. **Add frontmatter** at the top of the file:
   ```markdown
   ---
   sidebar_position: 16
   ---

   # My New Page

   Your content here...
   ```

3. **Add to sidebar** in `docs/sidebars.js`:
   ```javascript
   {
     type: 'doc',
     id: 'my-new-page',
     label: 'My New Page',
   }
   ```

### Editing Existing Pages

1. Find the markdown file in `docs/docs/`
2. Edit the content
3. Save the file
4. Changes will appear immediately in development mode

### Writing Style Guide

- Use clear, concise language
- Include code examples with syntax highlighting
- Use proper markdown formatting
- Add meaningful headings
- Include links to related documentation

### Code Examples

Use fenced code blocks with language specification:

````markdown
```php
function example_function() {
    return true;
}
```
````

Supported languages: `php`, `javascript`, `bash`, `json`, `css`, `html`, `markdown`, and more.

### Admonitions

Use admonitions for notes, tips, warnings:

```markdown
:::note
This is a note
:::

:::tip
This is a helpful tip
:::

:::warning
This is a warning
:::

:::danger
This is dangerous
:::
```

## Documentation Structure

```
docs/
├── docs/                      # Markdown documentation files
│   ├── intro.md              # Homepage/Introduction
│   ├── quick-start.md        # Getting started
│   ├── folder-structure.md   # Project structure
│   ├── customization.md      # Customization guide
│   ├── code-examples.md      # Code examples overview
│   ├── file-operations.md    # File handling
│   ├── data-looping.md       # Data iteration
│   ├── bulk-page-creation.md # Bulk operations
│   ├── custom-post-types.md  # CPT guide
│   ├── shortcodes.md         # Shortcode examples
│   ├── ajax-handlers.md      # AJAX implementation
│   ├── resources.md          # External resources
│   ├── testing.md            # Testing guide
│   ├── contributing.md       # Contribution guide
│   └── changelog.md          # Version history
├── src/
│   └── css/
│       └── custom.css        # Custom styles
├── static/
│   └── img/                  # Static images
│       ├── logo.svg          # Site logo
│       └── favicon.ico       # Favicon
├── docusaurus.config.js      # Main configuration
├── sidebars.js               # Sidebar navigation
├── package.json              # Dependencies
├── Dockerfile                # Docker configuration
├── docker-compose.yml        # Docker Compose config
└── README.md                 # Documentation README
```

## Configuration

### Main Configuration

Edit `docusaurus.config.js` to change:
- Site title and tagline
- URL and base path
- Theme colors
- Navbar items
- Footer content
- Plugin settings

### Sidebar Configuration

Edit `sidebars.js` to:
- Add/remove pages from sidebar
- Create categories
- Reorder pages
- Customize labels

### Custom Styling

Edit `src/css/custom.css` to:
- Change color scheme
- Adjust typography
- Add custom CSS

## Troubleshooting

### Port Already in Use

If port 3000 is occupied:

**npm:**
```bash
npm start -- --port 3001
```

**Docker:**
Edit `docker-compose.yml` and change:
```yaml
ports:
  - "3001:3000"
```

### Clear Cache

If experiencing issues:

**npm:**
```bash
npm run clear
npm start
```

**Docker:**
```bash
docker-compose down -v
docker-compose build --no-cache
docker-compose up
```

### Build Errors

Check for:
- Missing dependencies: Run `npm install`
- Broken links: Check markdown files
- Invalid frontmatter: Verify YAML syntax
- Node.js version: Ensure Node.js 18+

### Docker Issues

Common solutions:
```bash
# Remove all containers and images
docker-compose down --volumes --remove-orphans
docker system prune -a

# Rebuild from scratch
docker-compose build --no-cache
docker-compose up
```

## Deployment

### GitHub Pages

**Live Documentation:** [https://cavan.github.io/WordPress-Plugin-Starter-Template/](https://cavan.github.io/WordPress-Plugin-Starter-Template/)

#### Automatic Deployment (Recommended)

This repository uses GitHub Actions to automatically deploy documentation to GitHub Pages.

**Automated deployment triggers:**
- When changes are pushed to the `main` branch affecting the `docs/` directory
- Manual workflow dispatch from the Actions tab

**Setup requirements:**
1. Enable GitHub Pages in repository settings
2. Set source to "GitHub Actions"
3. See [GITHUB-PAGES-SETUP.md](../GITHUB-PAGES-SETUP.md) for detailed instructions

#### Manual Deployment

You can also deploy manually using Docusaurus:

1. **Configure** `docusaurus.config.js` (already configured):
   ```javascript
   url: 'https://cavan.github.io',
   baseUrl: '/WordPress-Plugin-Starter-Template/',
   organizationName: 'Cavan',
   projectName: 'WordPress-Plugin-Starter-Template',
   ```

2. **Deploy:**
   ```bash
   npm run deploy
   ```
   
   Note: Requires GitHub authentication and repository write access.

### Other Hosting

Build and upload the static files:

```bash
npm run build
# Upload the build/ directory to your hosting service
```

Supported platforms:
- Netlify
- Vercel
- AWS S3
- Azure Static Web Apps
- Any static hosting service

## npm Scripts Reference

| Command | Description |
|---------|-------------|
| `npm start` | Start development server |
| `npm run build` | Build for production |
| `npm run serve` | Serve production build locally |
| `npm run clear` | Clear cache |
| `npm run deploy` | Deploy to GitHub Pages |

## Docker Commands Reference

| Command | Description |
|---------|-------------|
| `docker-compose up` | Start container (foreground) |
| `docker-compose up -d` | Start container (background) |
| `docker-compose down` | Stop container |
| `docker-compose logs -f` | View logs |
| `docker-compose build` | Rebuild image |
| `docker-compose down -v` | Stop and remove volumes |

## Resources

- [Docusaurus Documentation](https://docusaurus.io/docs)
- [Markdown Guide](https://www.markdownguide.org/)
- [MDX Documentation](https://mdxjs.com/)

## Getting Help

If you encounter issues:

1. Check this guide
2. Review [Docusaurus documentation](https://docusaurus.io/docs)
3. Open an issue in the repository
4. Ask in discussions

## License

This documentation is part of the WordPress Plugin Starter Template and is licensed under GPL v2 or later.
