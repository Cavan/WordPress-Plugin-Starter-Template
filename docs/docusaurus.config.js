// @ts-check
// Note: type annotations allow type checking and IDEs autocompletion

/** @type {import('@docusaurus/types').Config} */
const config = {
  title: 'WordPress Plugin Starter Template',
  tagline: 'A comprehensive starter template for WordPress plugin development',
  favicon: 'img/favicon.ico',

  // Set the production url of your site here
  url: 'https://cavan.github.io',
  // Set the /<baseUrl>/ pathname under which your site is served
  baseUrl: '/WordPress-Plugin-Starter-Template/',

  // GitHub pages deployment config.
  organizationName: 'Cavan',
  projectName: 'WordPress-Plugin-Starter-Template',

  onBrokenLinks: 'warn',
  onBrokenMarkdownLinks: 'warn',

  // Even if you don't use internalization, you can use this field to set useful
  // metadata like html lang. For example, if your site is Chinese, you may want
  // to replace "en" with "zh-Hans".
  i18n: {
    defaultLocale: 'en',
    locales: ['en'],
  },

  presets: [
    [
      'classic',
      /** @type {import('@docusaurus/preset-classic').Options} */
      ({
        docs: {
          routeBasePath: '/',
          sidebarPath: require.resolve('./sidebars.js'),
          editUrl: 'https://github.com/Cavan/WordPress-Plugin-Starter-Template/tree/main/docs/',
        },
        blog: false,
        theme: {
          customCss: require.resolve('./src/css/custom.css'),
        },
      }),
    ],
  ],

  themeConfig:
    /** @type {import('@docusaurus/preset-classic').ThemeConfig} */
    ({
      navbar: {
        title: 'WP Plugin Starter',
        logo: {
          alt: 'WordPress Plugin Starter Logo',
          src: 'img/logo.svg',
        },
        items: [
          {
            type: 'docSidebar',
            sidebarId: 'tutorialSidebar',
            position: 'left',
            label: 'Documentation',
          },
          {
            href: 'https://github.com/Cavan/WordPress-Plugin-Starter-Template',
            label: 'GitHub',
            position: 'right',
          },
        ],
      },
      footer: {
        style: 'dark',
        links: [
          {
            title: 'Docs',
            items: [
              {
                label: 'Getting Started',
                to: '/',
              },
              {
                label: 'Code Examples',
                to: '/code-examples',
              },
            ],
          },
          {
            title: 'Resources',
            items: [
              {
                label: 'WordPress Plugin Handbook',
                href: 'https://developer.wordpress.org/plugins/',
              },
              {
                label: 'WordPress Code Reference',
                href: 'https://developer.wordpress.org/reference/',
              },
            ],
          },
          {
            title: 'More',
            items: [
              {
                label: 'GitHub',
                href: 'https://github.com/Cavan/WordPress-Plugin-Starter-Template',
              },
              {
                label: 'Contributing',
                to: '/contributing',
              },
            ],
          },
        ],
        copyright: `Copyright © ${new Date().getFullYear()} WordPress Plugin Starter Template. Built with Docusaurus.`,
      },
      prism: {
        theme: require('prism-react-renderer').themes.github,
        darkTheme: require('prism-react-renderer').themes.dracula,
        additionalLanguages: ['php', 'bash', 'json'],
      },
    }),
};

module.exports = config;
