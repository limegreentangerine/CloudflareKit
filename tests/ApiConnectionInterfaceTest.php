<?php

namespace Cloudflare\Tests;

use PHPUnit\Framework\TestCase;
use Cloudflare\Api\ConnectionInterface;

final class ApiConnectionInterfaceTest extends TestCase
{
    public function testConstructorAddsDefaultHeadersForJsonConnections(): void
    {
        $connection = new class('https://api.example.test/', 'json', ['Authorization' => 'Bearer token']) extends ConnectionInterface {};

        self::assertSame('https://api.example.test/', $connection->getBaseUrl());
        self::assertSame('json', $connection->getFormat());
        self::assertSame(
            [
                'Authorization' => 'Bearer token',
                'Cache-Control' => 'no-cache',
                'Content-Type' => 'application/json',
            ],
            $connection->getHeaders(),
        );
    }

    public function testConstructorDoesNotOverwriteExplicitHeaders(): void
    {
        $connection = new class('https://api.example.test', 'xml', [ 'Cache-Control' => 'max-age=60', 'Content-Type' => 'application/custom', ]) extends ConnectionInterface {};

        self::assertSame(
            [
                'Cache-Control' => 'max-age=60',
                'Content-Type' => 'application/custom',
            ],
            $connection->getHeaders(),
        );
    }

    public function testSettersAreFluentAndMergeHeadersAndResponseFormats(): void
    {
        $connection = new class('https://api.example.test', 'json') extends ConnectionInterface {};

        self::assertSame($connection, $connection->setBaseUrl('https://new.example.test'));
        self::assertSame($connection, $connection->setFormat('xml'));
        self::assertSame($connection, $connection->setHeaders(['X-Test' => 'yes']));
        self::assertSame($connection, $connection->setResponseFormats(['csv']));

        self::assertSame('https://new.example.test', $connection->getBaseUrl());
        self::assertSame('xml', $connection->getFormat());
        self::assertSame('yes', $connection->getHeaders()['X-Test']);
        self::assertContains('json', $connection->getResponseFormats());
        self::assertContains('xml', $connection->getResponseFormats());
        self::assertContains('csv', $connection->getResponseFormats());
    }
}
