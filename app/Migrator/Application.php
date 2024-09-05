<?php

namespace App\Migrator;

use Log;

class Application
{
    public function __invoke()
    {
        Log::error('__invoke do Application');
    }
}
