<?php

namespace CloudflareKit\Events;

use Core;
use UserGroup;
use Concrete\Core\User\User;
use CloudflareKit\Log\CloudflareLog;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Config\Repository\Liaison;
use CloudflareKit\Api\Connection as CloudflareApi;

class Cache
{
    /**
     * Get the value of config
     *
     * @return ?Liaison
     */
    public static function getConfig(): ?Liaison
    {
        $pkg = Core::make(PackageService::class)->getByHandle('cloudflare_kit');
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
    public static function enableDevMode(User $user): void
    {
        $adminGroup = UserGroup::getByName('Administrators');

        if (self::getActivate() && self::useDevMode() && $user->inGroup($adminGroup)) {
            $response = self::getApi()->setDevelopmentMode('on');
            $body = json_decode($response->getContent());

            if ($body->success) {
                Core::make(CloudflareLog::class)
                    ->getLogger()
                    ->addInfo('Cloudflare development mode activated.');
            } else {
                Core::make(CloudflareLog::class)
                    ->getLogger()
                    ->addWarning('Cloudflare development mode failed to activate.');
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
                Core::make(CloudflareLog::class)
                    ->getLogger()
                    ->addInfo('Cloudflare development mode deactivated.');
            } else {
                Core::make(CloudflareLog::class)
                    ->getLogger()
                    ->addWarning('Cloudflare development mode failed to deactivate.');
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
                Core::make(CloudflareLog::class)
                    ->getLogger()
                    ->addInfo('Cloudflare cache successfully cleared by force.');
            } else {
                Core::make(CloudflareLog::class)
                    ->getLogger()
                    ->addWarning('Cloudflare cache failed to be cleared by force.');
                Core::make(CloudflareLog::class)
                    ->getLogger()
                    ->addInfo(json_encode($body));
            }
        }
    }
}
