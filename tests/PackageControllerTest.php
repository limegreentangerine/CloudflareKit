<?php

namespace Cloudflare\Tests;

use PHPUnit\Framework\TestCase;
use Concrete\Core\Package\Package;

final class PackageControllerTest extends TestCase
{
    public function testRenamedPackageControllerUsesTheCloudflareKitHandle(): void
    {
        if (!defined('C5_EXECUTE')) {
            define('C5_EXECUTE', true);
        }
        if (!defined('DIR_PACKAGES_CORE')) {
            define('DIR_PACKAGES_CORE', '');
        }
        if (!defined('REL_DIR_PACKAGES_CORE')) {
            define('REL_DIR_PACKAGES_CORE', '');
        }
        if (!defined('DIR_PACKAGES')) {
            define('DIR_PACKAGES', '');
        }
        if (!defined('REL_DIR_PACKAGES')) {
            define('REL_DIR_PACKAGES', '');
        }

        require_once dirname(__DIR__) . '/controller.php';

        $controller = new \ReflectionClass(\Concrete\Package\CloudflareKit\Controller::class);

        self::assertTrue($controller->isSubclassOf(Package::class));
        self::assertSame('cloudflare_kit', $controller->getDefaultProperties()['pkgHandle']);
        self::assertSame(
            ['src' => '\CloudflareKit'],
            $controller->getDefaultProperties()['pkgAutoloaderRegistries'],
        );
    }
}
