<?php

namespace Cloudflare\Tests;

use PHPUnit\Framework\TestCase;
use CloudflareKit\Api\Connection;
use ClassKit\Api\Response\ErrorResponse;
use ClassKit\Api\Response\Response as ApiResponse;

final class ConnectionTest extends TestCase
{
    public function testPurgeCacheRequestsFullZonePurge(): void
    {
        $connection = new TestConnection();
        $connection->setZoneId('zone-123');

        $connection->purgeCache();

        self::assertSame('POST', $connection->method);
        self::assertSame('/zones/zone-123/purge_cache', $connection->url);
        self::assertSame(['purge_everything' => true], $connection->data);
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

    public function testGetZoneId(): void
    {
        $connection = new TestConnection();
        $connection->setZoneId('zone-456');

        self::assertSame('zone-456', $connection->getZoneId());
    }

    public function testSetZoneIdReturnsConnection(): void
    {
        $connection = new TestConnection();

        $result = $connection->setZoneId('zone-789');

        self::assertSame($connection, $result);
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
