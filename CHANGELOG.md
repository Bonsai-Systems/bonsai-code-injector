# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

## [Unreleased]

## [1.1.2] - 2026-09-24

### Fixed
- [composer.json, vendor/] Fatal error (`Cannot declare class ComposerAutoloaderInit057850a63dccc1b5ea3cf2a346d50db8`) when active alongside Bonsai ActiveCampaign, whose `vendor/` was copied from this plugin and so shared the same autoloader class. The 1.1.1 fix (unique `name`) didn't hold because Composer reuses the suffix already in `vendor/autoload.php`. Set a fixed `config.autoloader-suffix` (`BonsaiCodeInjector`) so the class is now always `ComposerAutoloaderInitBonsaiCodeInjector`.

### Added
- [bonsai-code-injector.php] Duplicate install guard: if a second copy of the plugin loads (e.g. a GitHub source zip unpacked as `bonsai-code-injector-1.1/`), it now bails early and shows an admin notice instead of causing a fatal error. Constants moved above the update checker so the guard runs before the Composer autoloader is required.

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
