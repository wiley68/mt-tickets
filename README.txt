=== MT Tickets ===
Contributors: ilkoivanov
Tags: block-theme, full-site-editing, fse, tickets, transport, bus, booking
Requires at least: 6.3
Tested up to: 6.6
Requires PHP: 8.0
Stable tag: 0.1.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A plugin-agnostic block theme starter for bus ticket / carrier ticketing websites.

== Description ==

MT Tickets is a modern WordPress block theme (Full Site Editing) designed as a clean visual foundation for ticketing projects, such as bus and carrier ticket sales.

Important: This theme intentionally contains NO ticket selling logic. The ticketing functionality should be implemented via a separate plugin, and that plugin should be able to work with other themes as well.

This theme focuses on:
* A clear header structure (top bar, header bar, hero area)
* A structured footer with multiple columns and a bottom menu area
* A design token approach via theme.json for consistent typography, spacing, and colors

== Internationalization ==

MT Tickets supports multiple languages and comes with translations for English and Bulgarian.

### Available Languages
* English (en_US) - default
* Bulgarian (bg_BG) - included

### Adding New Languages

1. **Using WP-CLI** (recommended):
   ```bash
   # Generate/update POT file
   wp i18n make-pot . languages/mt-tickets.pot --domain=mt-tickets --exclude=node_modules,vendor

   # Create PO file for a new language (e.g., German)
   wp i18n make-po languages/mt-tickets.pot languages/mt-tickets-de_DE.po

   # Generate MO file
   wp i18n make-mo languages/mt-tickets-de_DE.po
   ```

2. **Using npm scripts** (if WP-CLI is not available):
   ```bash
   npm install
   npm run i18n
   ```

3. **Manual translation**:
   - Copy `languages/mt-tickets.pot` to `languages/mt-tickets-[locale].po`
   - Edit the PO file with a translation editor like Poedit
   - Save to generate the MO file automatically

### Translating Theme Strings

When adding new translatable strings in PHP files, use WordPress gettext functions:
```php
__('Text to translate', 'mt-tickets')
_e('Text to echo', 'mt-tickets')
_x('Context-specific text', 'context', 'mt-tickets')
_n('Single item', 'Multiple items', $count, 'mt-tickets')
```

== Installation ==

1. Upload the `mt-tickets` folder to the `/wp-content/themes/` directory, or install via Appearance > Themes > Add New > Upload Theme.
2. Activate the theme through Appearance > Themes.
3. Open Appearance > Editor to customize header, footer, and global styles.

== Frequently Asked Questions ==

= Does MT Tickets include ticket sales functionality? =
No. MT Tickets is a theme only. Ticket sales logic should be provided by a plugin.

= Can I use the ticketing plugin with another theme? =
Yes. The plugin should be theme-independent by design.

== Changelog ==

= 0.1.0 =
* Initial release: block theme skeleton, base templates, template parts, and theme.json.

== Credits ==

Developed by Ilko Ivanov.

== License ==

GPL-2.0-or-later. See LICENSE.md for details.
