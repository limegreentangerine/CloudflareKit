# Cloudflare for Concrete CMS

Cloudflare integrations for Concrete CMS, including cache management,
Development Mode controls, and Cloudflare Turnstile CAPTCHA.

## Requirements

- PHP 8.4 or newer
- Concrete CMS 9.5.0 or newer
- A Cloudflare API token with the permissions needed for the features you use

## Installation

From the root of your Concrete CMS project, install the package with Composer:

```sh
composer require limegreentangerine/cloudflare_kit
```

Then install **Cloudflare** from the Concrete CMS dashboard under
**Extend concrete5**. Package installation adds the **Cloudflare** dashboard
page and registers **Cloudflare Turnstile** as a CAPTCHA library.

## Configuration

Open **Dashboard > Cloudflare** and configure the following:

| Setting              | Description                                                                                                               |
| -------------------- | ------------------------------------------------------------------------------------------------------------------------- |
| Activate             | Enables the package's event-driven Cloudflare actions.                                                                    |
| Use Development Mode | Enables Development Mode for administrators on login and disables it on logout when `cloudflare.use_dev_mode` is enabled. |
| API Base URL         | Cloudflare API URL. Defaults to `https://api.cloudflare.com/client/v4`.                                                   |
| Zone ID              | The Cloudflare Zone ID for the site.                                                                                      |
| API Token            | The token used to authenticate Cloudflare API requests.                                                                   |

Create a scoped API token rather than using a global API key. Allow Zone
Settings read and edit access (`Zone.Zone Settings`) for Development Mode.
Allow Cache Purge access (`Zone.Cache Purge`) if you want Concrete CMS cache
flushes to purge Cloudflare's cache.

When the package is active and `cloudflare.use_dev_mode` is enabled, an
administrator login enables Development Mode and logging out disables it.
Flushing the Concrete CMS cache purges the configured Cloudflare zone's cache.

## API

The package exposes these operations through `CloudflareKit\Api\Connection`:

```php
use CloudflareKit\Api\Connection;

$cloudflare = new Connection();

$status = $cloudflare->getDevelopmentMode();
$cloudflare->setDevelopmentMode('on'); // Set to 'off' to disable.
$cloudflare->purgeCache();
```

Each method returns a Symfony `Response` containing the Cloudflare API
response. API error responses cause a `RuntimeException`.

## Cloudflare Turnstile

Configure the Turnstile site key, secret key, theme, size, execution mode, and
appearance in Concrete CMS's CAPTCHA settings. Then select **Cloudflare
Turnstile** wherever Concrete CMS offers a CAPTCHA provider. Turnstile tokens
are verified server-side using Cloudflare's Siteverify endpoint.

## Development

Install the development dependencies and run the test suite:

```sh
composer install
composer test
```

Other useful Composer scripts:

```sh
composer format:check  # Check PHP and JavaScript formatting
composer format        # Apply PHP and JavaScript formatting
composer test-coverage # Run PHPUnit with text coverage output
```

## License

This project is released under the MIT License. See [LICENSE.TXT](LICENSE.TXT)
for the full text.
