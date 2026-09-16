<?php

namespace Cloudflare\Log;

class CloudflareLog extends Logger
{
    public function __construct()
    {
        parent::__construct('cloudflare');
    }
}
