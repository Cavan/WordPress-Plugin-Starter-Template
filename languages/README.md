# Languages Directory

This directory is for translation files (.pot, .po, .mo files).

## Creating Translation Files

To make your plugin translatable:

1. **Use translation functions in your code:**
   - `__( 'Text', 'wp-plugin-starter' )` - Returns translated string
   - `_e( 'Text', 'wp-plugin-starter' )` - Echoes translated string
   - `esc_html__( 'Text', 'wp-plugin-starter' )` - Returns escaped translated string
   - `esc_html_e( 'Text', 'wp-plugin-starter' )` - Echoes escaped translated string

2. **Generate .pot file using WP-CLI:**
   ```bash
   wp i18n make-pot . languages/wp-plugin-starter.pot
   ```

3. **Create language-specific .po files:**
   - Copy .pot file to .po (e.g., `wp-plugin-starter-es_ES.po`)
   - Translate strings using Poedit or similar tool

4. **Generate .mo files:**
   - .mo files are compiled versions of .po files
   - Most translation tools generate these automatically

## Translation Tools

- [Poedit](https://poedit.net/) - Popular translation editor
- [Loco Translate](https://wordpress.org/plugins/loco-translate/) - WordPress plugin for translating themes and plugins
- [WP-CLI i18n](https://developer.wordpress.org/cli/commands/i18n/) - Command-line translation tools

## More Information

- [WordPress Plugin Handbook - Internationalization](https://developer.wordpress.org/plugins/internationalization/)
- [WordPress i18n Documentation](https://codex.wordpress.org/I18n_for_WordPress_Developers)
