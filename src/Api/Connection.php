<?php

namespace Cloudflare\Api;

use Core;
use Concrete\Core\Entity\Package;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Config\Repository\Liaison;
use Symfony\Component\HttpFoundation\Response;

class Connection extends ConnectionInterface
{
    protected Package $pkg;

    protected Liaison $config;

    protected string $zoneId;

    protected string $apiToken;

    protected string $format = 'json';

    public function __construct()
    {
        $this->pkg = Core::make(PackageService::class)->getByHandle('cloudflare');
        if (!$this->pkg) {
            return;
        }

        $this->config = $this->pkg->getFileConfig();

        $this->setZoneID($this->config->get('cloudflare.zone_id'));
        $this->setApiToken($this->config->get('cloudflare.token'));
        $headers = [
            'Authorization' => 'Bearer ' . $this->getApiToken(),
        ];

        parent::__construct($this->config->get('cloudflare.base_url'), $this->format, $headers);
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
     * Get the value of apiToken
     *
     * @return string
     */
    public function getApiToken(): string
    {
        return $this->apiToken;
    }

    /**
     * Set the value of apiToken
     *
     * @param string $apiToken
     *
     * @return self
     */
    public function setApiToken(string $apiToken): self
    {
        $this->apiToken = $apiToken;

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
        return $response;
    }
}
