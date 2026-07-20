# Bonsai Code Injector

A minimal WordPress plugin for pasting tracking and verification code into a site's `<head>` and immediately after `<body>` — without editing theme files.

## Features

- Settings page under **Settings → Code Injector**
- **Header Code** field — printed as early as possible inside `<head>` on every front-end page (`wp_head`, priority 1). Use for the GA4 `gtag.js` snippet, the Google Tag Manager `<script>` block, or search-console-style meta verification tags.
- **Body Code** field — printed immediately after the opening `<body>` tag via `wp_body_open`. Use for the Google Tag Manager `<noscript>` snippet.
- Access restricted to `manage_options` by default (filterable via `bonsai_code_injector_capability`).
- Options removed on uninstall.

## Requirements

- WordPress 6.0+
- PHP 8.0+
- Theme must call `wp_body_open()` in `header.php` for the Body Code field to output. Bonsai base themes do this by default.

## Usage

1. Activate the plugin.
2. Go to **Settings → Code Injector**.
3. Paste your GA4/GTM/other tracking snippets into the relevant field and save.

### Google Tag Manager

- Paste the `<script>...</script>` install snippet into **Header Code**.
- Paste the `<noscript>...</noscript>` install snippet into **Body Code**.

### GA4 (gtag.js, no GTM)

- Paste the full `gtag.js` snippet into **Header Code**. No Body Code entry is needed.

## Security Note

This plugin intentionally outputs the saved code unescaped on the front end — that is its purpose. Access is restricted to users who can `manage_options` (site administrators). Do not lower this capability without understanding that anyone who can edit these fields can run arbitrary JavaScript on every page of the site.

## Data Structure

Two options are stored:

- `bci_header_code` (string)
- `bci_body_code` (string)

Both are deleted when the plugin is uninstalled.
