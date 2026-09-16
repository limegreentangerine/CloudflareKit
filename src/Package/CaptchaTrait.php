<?php

namespace Cloudflare\Package;

use Concrete\Core\Captcha\Library as CaptchaLibrary;

trait CaptchaTrait
{
    /**
     * Add Captcha Library
     *
     * @param string  $handle
     * @param string  $name
     * @param ?object $pkg
     *
     * @return CaptchaLibrary
     */
    protected function addCaptchaLibrary(string $handle, string $name, $pkg): CaptchaLibrary
    {
        $captcha = CaptchaLibrary::getByHandle($handle);

        if (!is_object($captcha)) {
            $captcha = CaptchaLibrary::add($handle, $name, $pkg);
        }

        return $captcha;
    }
}
