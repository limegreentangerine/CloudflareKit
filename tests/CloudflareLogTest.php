<?php

namespace Cloudflare\Tests;

use PHPUnit\Framework\TestCase;
use CloudflareKit\Log\CloudflareLog;

final class CloudflareLogTest extends TestCase
{
    public function testCloudflareLogClassExists(): void
    {
        self::assertTrue(class_exists(CloudflareLog::class));
    }

    public function testCloudflareLogExtendsLogger(): void
    {
        self::assertTrue(
            is_subclass_of(CloudflareLog::class, 'ClassKit\Log\Logger'),
            'CloudflareLog should extend ClassKit\Log\Logger',
        );
    }
}
