/**
 * Creating a sidebar enables you to:
 - create an ordered group of docs
 - render a sidebar for each doc of that group
 - provide next/previous navigation

 The sidebars can be generated from the filesystem, or explicitly defined here.

 Create as many sidebars as you want.
 */

// @ts-check

/** @type {import('@docusaurus/plugin-content-docs').SidebarsConfig} */
const sidebars = {
  tutorialSidebar: [
    {
      type: 'doc',
      id: 'intro',
      label: 'Introduction',
    },
    {
      type: 'category',
      label: 'Getting Started',
      items: [
        'quick-start',
        'folder-structure',
        'customization',
        'enable-local-search',
      ],
    },
    {
      type: 'category',
      label: 'Code Examples',
      items: [
        'code-examples',
        'file-operations',
        'data-looping',
        'bulk-page-creation',
      ],
    },
    {
      type: 'category',
      label: 'Common Operations',
      items: [
        'custom-post-types',
        'shortcodes',
        'ajax-handlers',
      ],
    },
    {
      type: 'doc',
      id: 'resources',
      label: 'Resources',
    },
    {
      type: 'doc',
      id: 'testing',
      label: 'Testing',
    },
    {
      type: 'doc',
      id: 'contributing',
      label: 'Contributing',
    },
    {
      type: 'doc',
      id: 'changelog',
      label: 'Changelog',
    },
  ],
};

module.exports = sidebars;
