import { defineConfig } from 'vitepress'

export default defineConfig({
  title: 'CakePHP Decimal',
  description: 'Decimal value objects for database columns and forms.',
  base: '/cakephp-decimal/',
  srcExclude: ['README.md'],
  head: [['link', { rel: 'icon', type: 'image/svg+xml', href: '/cakephp-decimal/favicon.svg' }]],
  themeConfig: {
    logo: '/logo.svg',
    nav: [
      { text: 'Installation', link: '/guide/installation' },
      { text: 'Getting started', link: '/guide/getting-started' },
      { text: 'Reference', link: '/reference/configuration' },
      { text: 'Decimal API', link: 'https://php-collective.github.io/decimal-object/' },
    ],
    sidebar: [
        { text: 'Installation', link: '/guide/installation' },
        { text: 'Getting started', link: '/guide/getting-started' },
        { text: 'Selected columns', link: '/guide/selected-columns' },
        { text: 'Forms', link: '/guide/forms' },
        { text: 'Formatting', link: '/guide/formatting' },
        { text: 'Configuration', link: '/reference/configuration' },
        { text: 'Conversion', link: '/reference/conversion' },
        { text: 'Upgrading', link: '/reference/upgrading' },
        { text: 'Decimal object API', link: 'https://php-collective.github.io/decimal-object/reference/api' }
    ],
    search: { provider: 'local' },
    socialLinks: [{ icon: 'github', link: 'https://github.com/dereuromark/cakephp-decimal' }],
    editLink: { pattern: 'https://github.com/dereuromark/cakephp-decimal/edit/cake6/docs/:path' },
    footer: { message: 'Released under the MIT License.', copyright: 'Copyright Mark Scherer' },
  },
})
