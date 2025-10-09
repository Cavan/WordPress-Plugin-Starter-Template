# GitHub Pages Setup Instructions

This document explains how to enable GitHub Pages for this repository to make the documentation site publicly accessible.

## Prerequisites

- Repository admin access to enable GitHub Pages
- The repository must be public (or you need GitHub Pro/Enterprise for private repos)

## Steps to Enable GitHub Pages

### 1. Enable GitHub Pages in Repository Settings

1. Go to your repository on GitHub
2. Click on **Settings** (in the repository menu)
3. In the left sidebar, click on **Pages** (under "Code and automation")
4. Under "Build and deployment":
   - **Source**: Select "GitHub Actions"
   - This will allow the workflow to deploy to GitHub Pages

### 2. Trigger the Deployment

The GitHub Actions workflow (`.github/workflows/deploy-docs.yml`) is configured to run automatically when:
- Changes are pushed to the `main` branch that affect files in the `docs/` directory
- The workflow is manually triggered via the Actions tab

To trigger the first deployment:

**Option A: Push a change to the docs**
```bash
# Make any small change in the docs directory
cd docs
echo "" >> README.md
git add .
git commit -m "Trigger docs deployment"
git push
```

**Option B: Manually trigger the workflow**
1. Go to the **Actions** tab in your repository
2. Click on "Deploy Documentation to GitHub Pages" workflow
3. Click "Run workflow" button
4. Select the `main` branch
5. Click "Run workflow"

### 3. Verify Deployment

After the workflow completes:
1. Go to **Settings** → **Pages**
2. You should see a message: "Your site is live at https://cavan.github.io/WordPress-Plugin-Starter-Template/"
3. Click the URL to view your documentation site

The deployment typically takes 1-3 minutes after the workflow completes.

## Workflow Details

The deployment workflow:
- **Triggers on**: 
  - Push to `main` branch (changes in `docs/` directory)
  - Manual workflow dispatch
- **Builds**: Installs dependencies and builds the Docusaurus site
- **Deploys**: Uses the official GitHub Pages deployment action
- **Artifacts**: Uploads the built site to GitHub Pages

## Troubleshooting

### Workflow Fails with Permissions Error

If you see an error about permissions:
1. Go to **Settings** → **Actions** → **General**
2. Scroll to "Workflow permissions"
3. Select "Read and write permissions"
4. Check "Allow GitHub Actions to create and approve pull requests"
5. Click "Save"

### Site Shows 404

1. Verify that GitHub Pages is enabled and source is set to "GitHub Actions"
2. Check that the workflow completed successfully in the Actions tab
3. Wait a few minutes for DNS propagation
4. Clear your browser cache

### Build Fails

1. Check the Actions tab for error logs
2. Ensure `docs/package-lock.json` is committed
3. Verify the build works locally:
   ```bash
   cd docs
   npm ci
   npm run build
   ```

## Local Development

To develop the documentation locally:

```bash
cd docs
npm install
npm start
```

The site will be available at `http://localhost:3000` with hot reload enabled.

## Updating the Documentation

To update the documentation:

1. Edit markdown files in `docs/docs/` directory
2. Test locally with `npm start`
3. Commit and push changes to `main` branch
4. The workflow will automatically deploy updates

## Configuration

The documentation configuration is in:
- `docs/docusaurus.config.js` - Main Docusaurus configuration
- `docs/sidebars.js` - Sidebar navigation structure
- `docs/src/css/custom.css` - Custom styling

### Important Configuration Values

In `docs/docusaurus.config.js`:
```javascript
url: 'https://cavan.github.io',
baseUrl: '/WordPress-Plugin-Starter-Template/',
organizationName: 'Cavan',
projectName: 'WordPress-Plugin-Starter-Template',
```

These values are already correctly configured for GitHub Pages deployment.

## Further Reading

- [GitHub Pages Documentation](https://docs.github.com/en/pages)
- [Docusaurus Deployment Guide](https://docusaurus.io/docs/deployment)
- [GitHub Actions Documentation](https://docs.github.com/en/actions)
