# WordPress Plugin Starter Template Documentation

This directory contains the documentation website built with [Docusaurus](https://docusaurus.io/).

## Prerequisites

- Node.js 20 or higher
- npm or yarn
- (Optional) Docker and Docker Compose

## Local Development

### Using npm

```bash
# Install dependencies
npm install

# Start development server
npm start
```

This command starts a local development server and opens up a browser window. Most changes are reflected live without having to restart the server.

The documentation will be available at `http://localhost:3000`.

### Using Docker

If you prefer to use Docker:

```bash
# Build and start the container
docker-compose up

# Or run in detached mode
docker-compose up -d
```

The documentation will be available at `http://localhost:3000`.

To stop the Docker container:

```bash
docker-compose down
```

## Build

### Production Build

```bash
npm run build
```

This command generates static content into the `build` directory and can be served using any static contents hosting service.

### Test Production Build Locally

```bash
npm run serve
```

This command serves the production build locally for testing.

## Project Structure

```
docs/
├── docs/                  # Documentation markdown files
│   ├── intro.md          # Introduction page
│   ├── quick-start.md    # Quick start guide
│   ├── folder-structure.md
│   ├── customization.md
│   ├── code-examples.md
│   ├── file-operations.md
│   ├── data-looping.md
│   ├── bulk-page-creation.md
│   ├── custom-post-types.md
│   ├── shortcodes.md
│   ├── ajax-handlers.md
│   ├── resources.md
│   ├── testing.md
│   ├── contributing.md
│   └── changelog.md
├── src/
│   └── css/
│       └── custom.css    # Custom styles
├── static/
│   └── img/              # Static images
├── docusaurus.config.js  # Docusaurus configuration
├── sidebars.js           # Sidebar navigation
├── package.json          # Node dependencies
├── Dockerfile            # Docker configuration
├── docker-compose.yml    # Docker Compose configuration
└── README.md             # This file
```

## Docker Configuration

### Dockerfile

The Dockerfile is configured to:
- Use Node.js 20 Alpine image
- Install dependencies
- Expose port 3000
- Run the development server

### docker-compose.yml

The Docker Compose file:
- Builds the Docker image
- Maps port 3000
- Mounts the local directory for live reload
- Preserves node_modules in a volume

## Configuration

### Docusaurus Configuration

Main configuration is in `docusaurus.config.js`:

- **Site metadata**: Title, tagline, URL, etc.
- **Theme configuration**: Navbar, footer, colors
- **Plugins**: Documentation, search, etc.

### Sidebar Configuration

Navigation is configured in `sidebars.js`:

```javascript
const sidebars = {
  tutorialSidebar: [
    'intro',
    {
      type: 'category',
      label: 'Getting Started',
      items: ['quick-start', 'folder-structure', 'customization'],
    },
    // ... more categories
  ],
};
```

## Writing Documentation

### Creating a New Page

1. Create a new `.md` file in the `docs/` directory:

```markdown
---
sidebar_position: 1
---

# Page Title

Your content here...
```

2. Add it to `sidebars.js` if you want it in the navigation.

### Markdown Features

Docusaurus supports:
- Standard Markdown
- MDX (Markdown with JSX)
- Code blocks with syntax highlighting
- Admonitions (notes, warnings, etc.)
- Tabs
- And more!

Example:

````markdown
```php
// PHP code with syntax highlighting
function example() {
    return true;
}
```

:::note
This is a note admonition.
:::

:::warning
This is a warning!
:::
````

## Customization

### Styling

Custom styles go in `src/css/custom.css`:

```css
:root {
  --ifm-color-primary: #0073aa;
  /* ... more custom properties */
}
```

### Adding Components

You can add React components in the `src/components/` directory and use them in your markdown files.

## Deployment

### GitHub Pages

This repository is configured to automatically deploy to GitHub Pages using GitHub Actions.

**Live Documentation:** [https://cavan.github.io/WordPress-Plugin-Starter-Template/](https://cavan.github.io/WordPress-Plugin-Starter-Template/)

#### Automatic Deployment

The documentation automatically deploys when:
- Changes are pushed to the `main` branch in the `docs/` directory
- The workflow is manually triggered

The GitHub Actions workflow (`.github/workflows/deploy-docs.yml`) handles:
1. Installing dependencies
2. Building the static site
3. Deploying to GitHub Pages

#### Setup Instructions

For first-time setup or troubleshooting, see [GITHUB-PAGES-SETUP.md](../GITHUB-PAGES-SETUP.md) in the root directory.

#### Manual Deployment

You can also deploy manually using the Docusaurus deploy command:

```bash
npm run deploy
```

Note: Manual deployment requires GitHub authentication and push access to the repository.

### Other Hosting

Build the static files and deploy to any static hosting service:

```bash
npm run build
# Upload the build/ directory to your hosting
```

Supported platforms:
- GitHub Pages
- Netlify
- Vercel
- AWS S3
- Azure Static Web Apps
- And more

## Troubleshooting

### Port Already in Use

If port 3000 is already in use:

```bash
# Use a different port
npm start -- --port 3001

# Or with Docker
# Edit docker-compose.yml and change ports to "3001:3000"
```

### Clear Cache

If you encounter issues:

```bash
npm run clear
npm start
```

### Docker Issues

If Docker build fails:

```bash
# Rebuild without cache
docker-compose build --no-cache

# Remove old containers and volumes
docker-compose down -v
```

## npm Scripts

| Script | Description |
|--------|-------------|
| `npm start` | Start development server |
| `npm run build` | Build for production |
| `npm run serve` | Serve production build locally |
| `npm run clear` | Clear cache |
| `npm run deploy` | Deploy to GitHub Pages |

## Resources

- [Docusaurus Documentation](https://docusaurus.io/docs)
- [Markdown Guide](https://www.markdownguide.org/)
- [MDX Documentation](https://mdxjs.com/)
- [Infima Documentation](https://infima.dev/) - Docusaurus CSS framework

## Contributing

See the main [CONTRIBUTING.md](../CONTRIBUTING.md) for contribution guidelines.

When contributing to documentation:

1. Follow the existing structure and style
2. Use clear, concise language
3. Include code examples where relevant
4. Test your changes locally before submitting
5. Check for broken links and typos

## License

This documentation is part of the WordPress Plugin Starter Template project and is licensed under GPL v2 or later.
