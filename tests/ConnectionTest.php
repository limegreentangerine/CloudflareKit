<?php

namespace Cloudflare\Tests;

use Cloudflare\Api\Connection;
use PHPUnit\Framework\TestCase;
use ClassKit\Api\Response\ErrorResponse;
use ClassKit\Api\Response\Response as ApiResponse;

final class ConnectionTest extends TestCase
{
    public function testDevelopmentModeRequestUsesZoneAndRequestedValue(): void
    {
        $connection = new TestConnection();
        $connection->setZoneId('zone-123');

        $response = $connection->getDevelopmentMode();

        self::assertSame('GET', $connection->method);
        self::assertSame('/zones/zone-123/settings/development_mode', $connection->url);
        self::assertNull($connection->data);
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

    public function testDevelopmentModeRequestThrowsWhenTheApiReturnsAnError(): void
    {
        $connection = new TestConnection();
        $connection->setZoneId('zone-123');
        $connection->response = new ErrorResponse('upstream failed', 503);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Cloudflare API request failed: /zones/zone-123/settings/development_mode returned HTTP 503 Service Unavailable',
        );

        $connection->getDevelopmentMode();
    }

    public function testSetDevelopmentModeThrowsWhenTheApiReturnsAnError(): void
    {
        $connection = new TestConnection();
        $connection->setZoneId('zone-123');
        $connection->response = new ErrorResponse('upstream failed', 503);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Cloudflare API request failed: /zones/zone-123/settings/development_mode returned HTTP 503 Service Unavailable',
        );

        $connection->setDevelopmentMode('on');
    }

    public function testPurgeCacheThrowsWhenTheApiReturnsAnError(): void
    {
        $connection = new TestConnection();
        $connection->setZoneId('zone-123');
        $connection->response = new ErrorResponse('upstream failed', 503);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Cloudflare API request failed: /zones/zone-123/purge_cache returned HTTP 503 Service Unavailable',
        );

        $connection->purgeCache();
    }
}

final class TestConnection extends Connection
{
    public string $method;
    public string $url;
    public ?array $data;
    public ApiResponse $response;

    public function __construct()
    {
        $this->response = new ApiResponse(['success' => true]);
    }

    public function makeRequest(
        string $method,
        string $apiUrl,
        ?array $data = null,
        ?array $headers = [],
    ): ApiResponse {
        $this->method = $method;
        $this->url = $apiUrl;
        $this->data = $data;

        if ($this->response instanceof ErrorResponse) {
            $this->response->setUrl($apiUrl);
        }

        return $this->response;
    }
}
