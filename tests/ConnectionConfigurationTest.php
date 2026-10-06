<?php

namespace Cloudflare\Tests;

use PHPUnit\Framework\TestCase;
use CloudflareKit\Api\Connection;

final class ConnectionConfigurationTest extends TestCase
{
    public function testBaseUrlAndZoneIdSettersAreFluent(): void
    {
        $connection = new class extends Connection {
            public function __construct() {}
        };

        self::assertSame($connection, $connection->setBaseUrl('https://new.example.test'));
        self::assertSame($connection, $connection->setZoneId('zone-123'));
        self::assertSame('https://new.example.test', $connection->getBaseUrl());
        self::assertSame('zone-123', $connection->getZoneId());
    }
}
