# Changelog

Notable changes to this project are documented here.

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
