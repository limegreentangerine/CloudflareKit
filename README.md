# Cloudflare

Cloudflare integrations for Concrete CMS, including Cloudflare API actions,
cache management, Development Mode support, and Cloudflare Turnstile CAPTCHA.

## Requirements

- PHP 8.4 or newer
- Concrete CMS 9.5.0 or newer
- A Cloudflare API token with the permissions required by the features you
  enable

## Installation

Install the package with Composer from the project root:

```bash
composer require limegreentangerine/cloudflare
```

Then install **Cloudflare** from the Concrete CMS dashboard under
**Dashboard > Extend concrete5**. Installation adds the Cloudflare dashboard
page and registers **Cloudflare Turnstile** as a CAPTCHA library.

## Configuration

Open **Dashboard > Cloudflare** and configure:

| Setting | Description |
| --- | --- |
| Activate | Enables the package’s event-driven Cloudflare actions. |
| Use Development Mode | Enables Development Mode for administrators while they are logged in. |
| API Base URL | Cloudflare API base URL. The default is `https://api.cloudflare.com/client/v4`. |
| Zone ID | The Cloudflare Zone ID for the site. |
| API Token | A Cloudflare API token used to authenticate API requests. |

The API token is stored in the package configuration. Use a token limited to
the permissions required by this package rather than a global API key. The
dashboard currently requires the `Zone.Zone Settings` permission for
Development Mode operations.

## Cloudflare actions

When the package is active:

- Logging in as a member of the **Administrators** group enables Cloudflare
  Development Mode when **Use Development Mode** is enabled.
- Logging out disables Development Mode.
- Flushing the Concrete CMS cache purges the Cloudflare cache for the
  configured zone.

The API connection is available through `Cloudflare\Api\Connection` and
provides:

```php
use Cloudflare\Api\Connection;

$cloudflare = new Connection();

$status = $cloudflare->getDevelopmentMode();
$cloudflare->setDevelopmentMode('on'); // or 'off'
$cloudflare->purgeCache();
```

Each method returns a Symfony `Response` containing the Cloudflare request
result.

## Cloudflare Turnstile

Installing the package registers a CAPTCHA library with the handle
`cfTurnstile`. Configure its site key, secret key, theme, size, execution mode,
and appearance in the Concrete CMS CAPTCHA settings, then select **Cloudflare
Turnstile** wherever Concrete CMS offers a CAPTCHA provider.

Turnstile verification is performed server-side against Cloudflare’s
`siteverify` endpoint. Missing tokens, unsuccessful verification, invalid
responses, and HTTP errors are rejected and logged.

## Development

Install development dependencies and run the test suite:

```bash
composer install
composer test
```

Useful Composer scripts include:

```bash
composer format:check  # Check PHP and JavaScript formatting
composer format        # Apply PHP and JavaScript formatting
composer test-coverage # Run PHPUnit with text coverage output
```

Continuous integration runs on pushes to `main` and manual workflow
dispatches. It uses PHP 8.4, installs dependencies with Composer, and runs
`composer test`. After the tests pass, the workflow dispatches a
`package-tests-passed` event to the downstream package repository.

## License

This package is released under the MIT License.
