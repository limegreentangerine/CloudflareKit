<?php

namespace Concrete\Package\Cloudflare;

use Core;
use Events;
use Concrete\Core\User\Event\User;
use Concrete\Core\Package\Package;
use Cloudflare\Package\CaptchaTrait;
use ClassKit\Package\Traits\PageTrait;
use Concrete\Core\Package\PackageService;
use Cloudflare\Events\Cache as CacheEvent;

class Controller extends Package
{
    use PageTrait;
    use CaptchaTrait;

    /**
     * The packages handle.
     * Note that this must be unique in the
     * entire concrete5 package ecosystem.
     *
     * @var string
     */
    protected $pkgHandle = 'cloudflare';

    /**
     * The packages version.
     *
     * @var string
     */
    protected $pkgVersion = '1.0.0';

    /**
     * The minimum Concrete version compatible with the package.
     * Override this value according to the minimum required version for your package.
     *
     * @var string
     */
    protected $appVersionRequired = '9.5.0';

    /**
     * The minimum PHP version compatible with the package.
     * Override this value according to the minimum required version for your package.
     *
     * @var string
     * @var string
     */
    protected $phpVersionRequired = '8.4';

    /**
     * Package service providers to register.
     *
     * eg. 'Concrete\Package\PackageHandle\Src\Providers\PackageServiceProvider'
     *
     * @var array
     */
    protected $providers = [];

    /**
     * An array describing the package dependencies.
     * Keys are package handles.
     * Values may be:
     * - false: this package can't be installed if the other package is already installed.
     * - true: this package can't be installed of the other package is not installed
     * - a string: this package can't be installed of the other package is not installed or it's installed with an older version
     * - an array with two strings, representing the minimum and the maximum version of the other package to be installed.
     *
     * @var array
     *
     * @example [
     *     // This package can't be installed if a package with handle other_package_1 is already installed.
     *     'other_package_1' => false,
     *     // This package can't be installed if a package with handle other_package_2 is not installed.
     *     'other_package_2' => true,
     *     // This package can't be installed if a package with handle other_package_3 is not installed, or it has a version prior to 1.0
     *     'other_package_3' => '1.0',
     *     // This package can't be installed if a package with handle other_package_4 is not installed, or it has a version prior to 2.0, or it has a version after 2.9
     *     'other_package_4' => ['2.0', '2.9'],
     * ]
     */
    protected $packageDependencies = [
        'class_kit' => true,
    ];

    /**
     * Package class autoloader registrations
     * The package install helper class, included with this boilerplate,
     * is activated by default.
     *
     * @see https://goo.gl/4wyRtH
     * @var array
     */
    protected $pkgAutoloaderRegistries = [
        'src' => '\Cloudflare',
    ];

    /**
     * Package tasks to register.
     *
     * eg. 'task_handle' => \PackageHandle\Command\Task\Controller\TaskHandleController::class,
     *
     * @var array
     */
    protected $tasks = [];

    protected function registerEvents()
    {
        Events::addListener('on_user_login', function (User $event) {
            $user = $event->getUserObject();
            CacheEvent::enableDevMode($user);
        });

        Events::addListener('on_user_logout', function () {
            CacheEvent::disableDevMode();
        });

        Events::addListener('on_cache_flush', function () {
            CacheEvent::forceCacheClear();
        });
    }

    protected function installOrUpgrade(\Concrete\Core\Entity\Package $pkg): void
    {
        // add dashboard page
        $this->addSinglePage('/dashboard/cloudflare', $pkg, t('Cloudflare'), t('Cloudflare API settings.'));

        // install CF Turnstile as Captcha option
        $this->addCaptchaLibrary('cfTurnstile', t('Cloudflare Turnstile'), $pkg);
    }

    public function getPackageName()
    {
        return t('Cloudflare');
    }

    public function getPackageDescription()
    {
        return t('Cloudflare API actions');
    }

    public function on_start()
    {
        $this->registerEvents();
    }

    /**
     * The packages install routine.
     */
    public function install()
    {
        $pkg = parent::install();
        $this->installOrUpgrade($pkg);
    }

    /**
     * The packages upgrade routine.
     */
    public function upgrade()
    {
        $pkg = Core::make(PackageService::class)->getByHandle($this->pkgHandle);
        parent::upgrade();
        $this->installOrUpgrade($pkg);
    }
}
