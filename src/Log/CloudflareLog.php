<?php

namespace CloudflareKit\Log;

use ClassKit\Log\Logger;

class CloudflareLog extends Logger
{
    public function __construct()
    {
        parent::__construct('cloudflare');
    }
}
