---
sidebar_position: 16
---

# Enable Local Search

This documentation site includes a self-hosted, client-side search functionality powered by the [@cmfcmf/docusaurus-search-local](https://github.com/cmfcmf/docusaurus-search-local) plugin.

## Features

The local search plugin provides:

- **Client-side search**: No server required, works entirely in the browser
- **Fast indexing**: Indexes documentation pages at build time
- **Privacy-friendly**: All search happens locally, no data sent to external services
- **Multi-language support**: Configured for English content

## Installation

If you're setting up the documentation site for the first time, follow these steps:

### Prerequisites

- Node.js 18 or higher
- npm or yarn package manager

### Install Dependencies

Navigate to the docs directory and install dependencies:

```bash
cd docs
npm install
```

Or if you prefer yarn:

```bash
cd docs
yarn install
```

## Building the Site

The search index is generated during the build process. To build the documentation site:

```bash
npm run build
```

This command:
1. Builds the static documentation site
2. Generates the search index from all documentation pages
3. Outputs the result to the `build/` directory

## Testing Locally

### Development Server

To start the development server:

```bash
npm run start
```

**Note**: The search functionality may not work fully in development mode. For testing the complete search experience, use the production build.

### Production Build

To test the production build locally:

```bash
# Build the site first
npm run build

# Serve the built site
npm run serve
```

The documentation site will be available at `http://localhost:3000` with full search functionality.

## Using the Search

Once the site is built and running:

1. Look for the **search bar** in the navigation header
2. Type your search query
3. Results will appear instantly as you type
4. Click on any result to navigate to that page

## Search Configuration

The search plugin is configured in `docusaurus.config.js` with the following settings:

```javascript
plugins: [
  [
    require.resolve('@cmfcmf/docusaurus-search-local'),
    {
      indexDocs: true,      // Index documentation pages
      indexBlog: false,     // Skip blog (we don't have one)
      indexPages: true,     // Index standalone pages
      language: 'en',       // English language support
    },
  ],
],
```

### Customization Options

You can modify the search behavior by editing the plugin configuration in `docusaurus.config.js`. Available options include:

- `indexDocs`: Whether to index documentation pages (default: true)
- `indexBlog`: Whether to index blog posts (default: false)
- `indexPages`: Whether to index standalone pages (default: true)
- `language`: Language code for search optimization (default: 'en')
- `maxSearchResults`: Maximum number of search results to display (default: 8)
- `lunr`: Advanced Lunr.js configuration options

For more configuration options, see the [plugin documentation](https://github.com/cmfcmf/docusaurus-search-local#configuration).

## Troubleshooting

### Search not appearing

If the search bar doesn't appear:
1. Make sure you've run `npm install` to install the plugin
2. Rebuild the site with `npm run build`
3. Check the browser console for any error messages

### Empty search results

If search returns no results:
1. Ensure the site has been built (not just started in dev mode)
2. Verify that the search index was generated during the build
3. Try rebuilding the site: `npm run clear && npm run build`

### Build errors

If you encounter build errors:
1. Ensure you're using a compatible Node.js version (18+)
2. Delete `node_modules` and reinstall: `rm -rf node_modules && npm install`
3. Clear the Docusaurus cache: `npm run clear`

## Docker Support

If you're using Docker, the search plugin works out of the box:

```bash
# Using Docker Compose
docker-compose up

# Or build and run manually
docker build -t wp-plugin-docs .
docker run -p 3000:3000 wp-plugin-docs
```

The search will be available after the container builds the site.

## Next Steps

- Explore the [Docusaurus documentation](https://docusaurus.io/docs) for more features
- Learn about [advanced search configuration](https://github.com/cmfcmf/docusaurus-search-local#configuration)
- Check out other [Docusaurus plugins](https://docusaurus.io/community/resources#plugins) to enhance your documentation
