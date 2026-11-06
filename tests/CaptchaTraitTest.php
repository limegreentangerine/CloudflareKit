<?php

namespace Cloudflare\Tests;

use PHPUnit\Framework\TestCase;
use CloudflareKit\Package\CaptchaTrait;

final class CaptchaTraitTest extends TestCase
{
    public function testTraitExists(): void
    {
        // Create a concrete class that uses the trait
        $class = new class {
            use CaptchaTrait;
        };

        self::assertTrue(trait_exists('CloudflareKit\Package\CaptchaTrait'));
    }

    public function testAddCaptchaLibraryMethodExists(): void
    {
        $class = new class {
            use CaptchaTrait;

            public function testMethod()
            {
                return method_exists($this, 'addCaptchaLibrary');
            }
        };

        self::assertTrue($class->testMethod());
    }
}
