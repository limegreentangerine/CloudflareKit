# Changelog

Notable changes to this project are documented here.

## [Unreleased]

### Changed

- Renamed the Composer package to `limegreentangerine/cloudflare_kit` and the
  PHP API namespace to `CloudflareKit`.
- Cloudflare API error responses now throw a `RuntimeException` with the
  failing endpoint and HTTP status.

### Tests

- Added coverage for the renamed Concrete CMS package controller, package
  handle, and class autoloader registration.
- Covered successful API request payloads and API error handling.

## [1.0.0] - 2026-10-05

### Added

- Concrete CMS package integration with a dashboard for Cloudflare account and
  zone settings.
- Cloudflare API operations for checking and changing zone Development Mode
  and purging the zone cache.
- Event-driven Development Mode controls for administrator login and logout,
  and cache purging when the Concrete CMS cache is flushed.
- Cloudflare Turnstile CAPTCHA integration with server-side token verification.
- PHPUnit tests for API connection behavior.
