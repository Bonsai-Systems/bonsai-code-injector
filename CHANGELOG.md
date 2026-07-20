# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

## [Unreleased]

### Added
- [bonsai-code-injector.php] Initial release: Settings page with Header Code and Body Code fields, output via `wp_head` (priority 1) and `wp_body_open`
- [bonsai-code-injector.php] Capability gate (`manage_options`, filterable via `bonsai_code_injector_capability`) on settings page, save handler and sanitize callback
- [bonsai-code-injector.php] Settings link added to the plugin's row on the Plugins screen
- [bonsai-code-injector.php] Options deleted on uninstall
- [composer.json, vendor/] Wired up YahnisElsts/plugin-update-checker (^5.6) so the plugin can self-update from GitHub releases via the wp-admin Plugins screen, matching `bonsai-maintenance`
