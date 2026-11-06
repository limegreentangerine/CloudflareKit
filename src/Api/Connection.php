<?php

namespace CloudflareKit\Api;

use Core;
use Concrete\Core\Entity\Package;
use ClassKit\Api\ConnectionController;
use ClassKit\Api\Response\ErrorResponse;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Config\Repository\Liaison;
use Concrete\Core\Error\UserMessageException;
use Symfony\Component\HttpFoundation\Response;

class Connection extends ConnectionController
{
    protected Package $pkg;

    protected Liaison $config;

    protected string $zoneId;

    public function __construct()
    {
        $this->pkg = Core::make(PackageService::class)->getByHandle('cloudflare_kit');
        if (!$this->pkg) {
            throw new UserMessageException(t('CloudflareKit package not installed.'), 404);
        }

        $baseUrl = 'https://api.cloudflare.com/client/v4';
        $zoneId = (isset($_ENV['CLOUDFLARE_ZONE_ID'])) ? $_ENV['CLOUDFLARE_ZONE_ID'] : ((isset($_SERVER['CLOUDFLARE_ZONE_ID'])) ? $_SERVER['CLOUDFLARE_ZONE_ID'] : false);
        $apiKey = (isset($_ENV['CLOUDFLARE_API_KEY'])) ? $_ENV['CLOUDFLARE_API_KEY'] : ((isset($_SERVER['CLOUDFLARE_API_KEY'])) ? $_SERVER['CLOUDFLARE_API_KEY'] : false);

        if (!$zoneId) {
            throw new UserMessageException(t('Cloudflare API key missing.'), 401);
        }

        if (!$apiKey) {
            throw new UserMessageException(t('Cloudflare API key missing.'), 401);
        }

        $this->setZoneID($zoneId);

        $headers = [
            'Authorization' => 'Bearer ' . $apiKey,
        ];

        parent::__construct($baseUrl, 'json', $headers);
    }

    /**
     * Get the value of zoneId
     *
     * @return string
     */
    public function getZoneId(): string
    {
        return $this->zoneId;
    }

    /**
     * Set the value of zoneId
     *
     * @param string $zoneId
     *
     * @return self
     */
    public function setZoneId(string $zoneId): self
    {
        $this->zoneId = $zoneId;

        return $this;
    }

    /**
     * Purge Cloudflare Cache
     *
     * @return Response
     */
    public function purgeCache(): Response
    {
        $url = sprintf('/zones/%s/purge_cache', $this->getZoneID());
        $response = $this->makeRequest('POST', $url, [
            'purge_everything' => true,
        ]);

        if ($response instanceof ErrorResponse) {
            throw new \RuntimeException(sprintf(
                'Cloudflare API request failed: %s returned HTTP %d %s',
                $response->getUrl(),
                $response->getStatusCode(),
                $response->getStatusText($response->getStatusCode()),
            ));
        }

        return $response;
    }
}
