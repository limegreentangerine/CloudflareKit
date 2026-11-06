<?php

namespace CloudflareKit\Events;

use Core;
use CloudflareKit\Log\CloudflareLog;
use CloudflareKit\Api\Connection as CloudflareApi;

class Cache
{
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
        return (bool) (isset($_ENV['CLOUDFLARE_ACTIVE'])) ? $_ENV['CLOUDFLARE_ACTIVE'] : ((isset($_SERVER['CLOUDFLARE_ACTIVE'])) ? $_SERVER['CLOUDFLARE_ACTIVE'] : false);
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
