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

        $this->config = $this->pkg->getFileConfig();

        $this->setZoneID($this->config->get('cloudflare.zone_id'));

        if (!getenv('CLOUDFLARE_API_KEY')) {
            throw new UserMessageException(t('Cloudflare API key missing.'), 401);
        }

        $headers = [
            'Authorization' => 'Bearer ' . getenv('CLOUDFLARE_API_KEY'),
        ];

        parent::__construct($this->config->get('cloudflare.base_url'), 'json', $headers);
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
     * Get Development Mode State
     *
     * @return Response
     */
    public function getDevelopmentMode(): Response
    {
        $url = sprintf('/zones/%s/settings/development_mode', $this->getZoneID());
        $response = $this->makeRequest('GET', $url);

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

    /**
     * Set Development Mode State
     *
     * @param string $value
     *
     * @return Response
     */
    public function setDevelopmentMode(string $value = 'off'): Response
    {
        $url = sprintf('/zones/%s/settings/development_mode', $this->getZoneID());
        $response = $this->makeRequest('PATCH', $url, [
            'value' => $value,
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
