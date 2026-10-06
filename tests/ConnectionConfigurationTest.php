<?php

namespace Cloudflare\Tests;

use Cloudflare\Api\Connection;
use PHPUnit\Framework\TestCase;

final class ConnectionConfigurationTest extends TestCase
{
    public function testZoneIdAndApiTokenSettersAreFluent(): void
    {
        $connection = new class extends Connection {
            public function __construct() {}
        };

        self::assertSame($connection, $connection->setBaseUrl('https://new.example.test'));
        self::assertSame($connection, $connection->setZoneId('zone-123'));
        self::assertSame($connection, $connection->setApiToken('token-456'));
        self::assertSame('https://new.example.test', $connection->getBaseUrl());
        self::assertSame('zone-123', $connection->getZoneId());
        self::assertSame('token-456', $connection->getApiToken());
    }
}
