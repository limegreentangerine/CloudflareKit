<?php

namespace Cloudflare\Tests;

use Cloudflare\Api\Connection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

final class ConnectionTest extends TestCase
{
    public function testDevelopmentModeRequestUsesZoneAndRequestedValue(): void
    {
        $connection = new TestConnection();
        $connection->setZoneId('zone-123');

        $response = $connection->getDevelopmentMode();

        self::assertSame('GET', $connection->method);
        self::assertSame('/zones/zone-123/settings/development_mode', $connection->url);
        self::assertSame([], $connection->data);
        self::assertSame(200, $response->getStatusCode());
    }

    public function testSetDevelopmentModeDefaultsToOff(): void
    {
        $connection = new TestConnection();
        $connection->setZoneId('zone-123');

        $connection->setDevelopmentMode();

        self::assertSame('PATCH', $connection->method);
        self::assertSame('/zones/zone-123/settings/development_mode', $connection->url);
        self::assertSame(['value' => 'off'], $connection->data);
    }

    public function testSetDevelopmentModeSendsProvidedValue(): void
    {
        $connection = new TestConnection();
        $connection->setZoneId('zone-123');

        $connection->setDevelopmentMode('on');

        self::assertSame(['value' => 'on'], $connection->data);
    }

    public function testPurgeCacheRequestsFullZonePurge(): void
    {
        $connection = new TestConnection();
        $connection->setZoneId('zone-123');

        $connection->purgeCache();

        self::assertSame('POST', $connection->method);
        self::assertSame('/zones/zone-123/purge_cache', $connection->url);
        self::assertSame(['purge_everything' => true], $connection->data);
    }
}

final class TestConnection extends Connection
{
    public string $method;
    public string $url;
    public array $data;

    public function __construct() {}

    protected function makeRequest(
        string $method,
        string $apiUrl,
        array $data = [],
        array $headers = [],
    ): JsonResponse {
        $this->method = $method;
        $this->url = $apiUrl;
        $this->data = $data;

        return new JsonResponse(['success' => true]);
    }
}
