<?php

namespace Cloudflare\Tests;

use CloudflareKit\Events\Cache;
use PHPUnit\Framework\TestCase;

final class CacheEventsTest extends TestCase
{
    public function testGetActivateReturnsFalseWhenEnvNotSet(): void
    {
        // Temporarily unset environment variables
        $oldActive = $_ENV['CLOUDFLARE_ACTIVE'] ?? null;
        $oldServer = $_SERVER['CLOUDFLARE_ACTIVE'] ?? null;

        unset($_ENV['CLOUDFLARE_ACTIVE']);
        unset($_SERVER['CLOUDFLARE_ACTIVE']);

        $result = Cache::getActivate();

        self::assertFalse($result);

        // Restore environment
        if ($oldActive !== null) {
            $_ENV['CLOUDFLARE_ACTIVE'] = $oldActive;
        }
        if ($oldServer !== null) {
            $_SERVER['CLOUDFLARE_ACTIVE'] = $oldServer;
        }
    }

    public function testGetActivateReturnsTrueWhenEnvIsSet(): void
    {
        // Set environment variable
        $_ENV['CLOUDFLARE_ACTIVE'] = true;

        $result = Cache::getActivate();

        self::assertTrue($result);

        // Clean up
        unset($_ENV['CLOUDFLARE_ACTIVE']);
    }

    public function testGetActivateReturnsTrueWhenServerVarIsSet(): void
    {
        // Set server variable
        $_SERVER['CLOUDFLARE_ACTIVE'] = true;

        $result = Cache::getActivate();

        self::assertTrue($result);

        // Clean up
        unset($_SERVER['CLOUDFLARE_ACTIVE']);
    }
}
