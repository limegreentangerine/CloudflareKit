<?php

namespace Cloudflare\Events;

use Core;
use Cloudflare\Log\CloudflareLog;
use Monolog\Logger as MonologLogger;
use Concrete\Core\Site\Config\Liaison;
use Concrete\Core\Package\PackageService;
use Cloudflare\Api\Connection as CloudflareApi;

class Cache
{
    protected MonologLogger $logger;

    public function __construct()
    {
        $this->logger = Core::make(CloudflareLog::class)->getLogger();
    }

    /**
     * Get the value of config
     *
     * @return ?Liaison
     */
    public static function getConfig(): ?Liaison
    {
        $pkg = Core::make(PackageService::class)->getByHandle('cloudflare');
        if (is_object($pkg)) {
            return $pkg->getFileConfig();
        }

        return null;
    }

    /**
     * Get the value of api
     *
     * @return CloudflareApi
     */
    public static function getApi(): CloudflareApi
    {
        return new CloudflareApi();
    }

    /**
     * Get Logger
     *
     * @return MonologLogger
     */
    public static function getLogger(): MonologLogger
    {
        return self::$logger;
    }

    /**
     * Get the value of activate
     *
     * @return bool
     */
    public static function getActivate(): bool
    {
        $config = self::getConfig();
        return ($config) ? $config->get('cloudflare.activate') : false;
    }

    /**
     * Use Dev Mode on use login/logout
     *
     * @return bool
     */
    public static function useDevMode(): bool
    {
        $config = self::getConfig();
        return ($config) ? $config->get('cloudflare.use_dev_mode') : false;
    }

    /**
     * Enable Dev Mode
     */
    public static function enableDevMode(): void
    {
        if (self::getActivate() && self::useDevMode()) {
            $response = self::getApi()->setDevelopmentMode('on');
            $body = json_decode($response->getContent());

            if ($body->success) {
                self::getLogger()->addInfo('Cloudflare development mode activated.');
            } else {
                self::getLogger()->addWarning('Cloudflare development mode failed to activate.');
            }
        }
    }

    /**
     * Disable Dev Mode
     */
    public static function disableDevMode(): void
    {
        if (self::getActivate() && self::useDevMode()) {
            $response = self::getApi()->setDevelopmentMode('off');
            $body = json_decode($response->getContent());

            if ($body->success) {
                self::getLogger()->addInfo('Cloudflare development mode deactivated.');
            } else {
                self::getLogger()->addWarning('Cloudflare development mode failed to deactivate.');
            }
        }
    }

    /**
     * Clear Cloudflare Cache
     */
    public static function forceCacheClear(): void
    {
        if (self::getActivate()) {
            $response = self::getApi()->purgeCache();
            $body = json_decode($response->getContent());

            if ($body->success) {
                self::getLogger()->addInfo('Cloudflare cache successfully cleared by force.');
            } else {
                self::getLogger()->addWarning('Cloudflare cache failed to be cleared by force.');
                self::getLogger()->addInfo(json_encode($body));
            }
        }
    }
}
