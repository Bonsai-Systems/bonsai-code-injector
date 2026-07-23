# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

## [Unreleased]

## [1.1.1] - 2026-07-23

### Fixed
- [composer.json] Fatal error (`Cannot declare class ComposerAutoloaderInit88bd1240a06e12371573341aa3549092, because the name is already in use`) when active alongside another Bonsai plugin bundling the same version of `yahnis-elsts/plugin-update-checker` (found via `cookie-consent-video-embed-CookieScript` on a live client site). This plugin's `composer.json` was byte-identical to several other Bonsai plugins', so Composer generated the same autoloader class name in every one of them. Added a unique `name` field to `composer.json` and regenerated `vendor/` from a clean install — new class name: `ComposerAutoloaderInit057850a63dccc1b5ea3cf2a346d50db8`.
- [bonsai-code-injector.php] Removed call to `setUpdateCheckInterval()`, which doesn't exist on the v5.7 `PluginUpdateChecker` class and caused a fatal error on activation; the check interval is now passed via the `$checkPeriod` argument to `buildUpdateChecker()` instead

### Added
- [bonsai-code-injector.php] Initial release: Settings page with Header Code and Body Code fields, output via `wp_head` (priority 1) and `wp_body_open`
- [bonsai-code-injector.php] Capability gate (`manage_options`, filterable via `bonsai_code_injector_capability`) on settings page, save handler and sanitize callback
- [bonsai-code-injector.php] Settings link added to the plugin's row on the Plugins screen
- [bonsai-code-injector.php] Options deleted on uninstall
- [composer.json, vendor/] Wired up YahnisElsts/plugin-update-checker (^5.6) so the plugin can self-update from GitHub releases via the wp-admin Plugins screen, matching `bonsai-maintenance`
