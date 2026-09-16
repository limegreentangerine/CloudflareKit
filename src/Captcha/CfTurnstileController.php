<?php

namespace Concrete\Package\Cloudflare\Captcha;

use Exception;
use Concrete\Core\View\View;
use IPLib\Address\AddressInterface;
use Concrete\Core\Captcha\CaptchaInterface;
use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Http\Client\Client as HttpClient;
use Concrete\Core\Logging\{Channels, LoggerAwareInterface, LoggerAwareTrait};

class CfTurnstileController extends AbstractController implements CaptchaInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected function generateWidget()
    {
        $config = $this->app->make('config');

        static $scriptAdded = false;

        if (!$scriptAdded) {
            View::getInstance()->addFooterItem(
                '<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" defer></script>',
            );
            $scriptAdded = true;
        }

        return sprintf(
            '<div class="cf-turnstile" data-sitekey="%s" data-theme="%s" data-size="%s" data-execution="%s" data-appearance="%s"></div>',
            h($config->get('captcha.cfTurnstile.site_key')),
            h($config->get('captcha.cfTurnstile.theme', 'auto')),
            h($config->get('captcha.cfTurnstile.size', 'normal')),
            h($config->get('captcha.cfTurnstile.execution', 'render')),
            h($config->get('captcha.cfTurnstile.appearance', 'always')),
        );
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Logging\LoggerAwareInterface::getLoggerChannel()
     */
    public function getLoggerChannel()
    {
        return Channels::CHANNEL_SPAM;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Captcha\CaptchaInterface::display()
     */
    public function display()
    {
        return $this->generateWidget();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Captcha\CaptchaInterface::showInput()
     */
    public function showInput()
    {
        return $this->generateWidget();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Captcha\CaptchaInterface::label()
     */
    public function label()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Captcha\CaptchaInterface::check()
     */
    public function check()
    {
        $config = $this->app->make('config');
        $httpClient = $this->app->make(HttpClient::class);

        $token = (string) $this->request->request->get('cf-turnstile-response');

        if ($token === '') {
            $this->logger->notice(t('CF Turnstile: missing token'));
            return false;
        }

        try {
            $response = $httpClient->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'form_params' => [
                    'secret' => $config->get('captcha.cfTurnstile.secret_key'),
                    'response' => $token,
                    'remoteip' => (string) ($this->app->make(AddressInterface::class)->__toString() ?? ''),
                ],
            ]);
        } catch (Exception $x) {
            $this->logger->alert(t('CF Turnstile HTTP error: %s', $x->getMessage()));
            return false;
        }

        if ($response->getStatusCode() !== 200) {
            $this->logger->alert(t('CF Turnstile: non-200 response (%s)', $response->getStatusCode()));
            return false;
        }

        $data = json_decode((string) $response->getBody(), true);

        if (!is_array($data) || json_last_error() !== JSON_ERROR_NONE) {
            $this->logger->alert(t('CF Turnstile: invalid JSON response'));
            return false;
        }

        if (empty($data['success'])) {
            $errors = implode(', ', $data['error-codes'] ?? ['unknown']);
            $this->logger->notice(t('CF Turnstile verification failed: %s', $errors));
            return false;
        }

        return true;
    }

    public function saveOptions(array $data)
    {
        $data = (is_array($data) ? $data : []) + [
            'site_key' => '',
            'secret_key' => '',
            'theme' => '',
            'size' => '',
            'execution' => '',
            'appearance' => '',
        ];
        $config = $this->app->make('config');
        $config->save('captcha.cfTurnstile.site_key', (string) $data['site_key']);
        $config->save('captcha.cfTurnstile.secret_key', (string) $data['secret_key']);
        $config->save('captcha.cfTurnstile.theme', (string) $data['theme']);
        $config->save('captcha.cfTurnstile.size', (string) $data['size']);
        $config->save('captcha.cfTurnstile.execution', (string) $data['execution']);
        $config->save('captcha.cfTurnstile.appearance', (string) $data['appearance']);
    }
}
