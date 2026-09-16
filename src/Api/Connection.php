<?php

namespace Cloudflare\Api;

use Core;
use Concrete\Core\Entity\Package;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Config\Repository\Liaison;

class Connection extends ConnectionInterface
{
    protected Package $pkg;

    protected Liaison $config;

    protected string $zoneId;

    protected string $apiToken;

    public function __construct()
    {
        $this->pkg = Core::make(PackageService::class)->getByHandle('cloudflare');
        if (!$this->pkg) {
            return;
        }

        $this->config = $this->pkg->getFileConfig();

        // $this->setZoneID($pkg->getFileConfig()->get('lgt_toolkit.cloudflare.zone_id'));
        // $this->setApiToken($pkg->getFileConfig()->get('lgt_toolkit.cloudflare.token'));
        // $format = 'json';
        // $headers = [
        //     'Authorization' => 'Bearer ' . $this->getApiToken()
        // ];

        // parent::__construct($pkg->getFileConfig()->get('lgt_toolkit.cloudflare.base_url'), $format, $headers);
    }

    /**
     * Get the value of zoneId
     *
     * @return string
     */
    public function getZoneId()
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
    public function setZoneId(string $zoneId)
    {
        $this->zoneId = $zoneId;

        return $this;
    }

    /**
     * Get the value of apiToken
     *
     * @return string
     */
    public function getApiToken()
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
    public function setApiToken(string $apiToken)
    {
        $this->apiToken = $apiToken;

        return $this;
    }

    /**
     * Get Development Mode State
     *
     * @return Response
     */
    public function getDevelopmentMode()
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
    public function setDevelopmentMode(string $value = 'off')
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
    public function purgeCache()
    {
        $url = sprintf('/zones/%s/purge_cache', $this->getZoneID());
        $response = $this->makeRequest('POST', $url, [
            'purge_everything' => true,
        ]);
        return $response;
    }
}
