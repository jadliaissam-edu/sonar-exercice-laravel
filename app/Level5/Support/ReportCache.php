<?php

namespace App\Level5\Support;

use Illuminate\Support\Facades\Cache;

class ReportCache
{
    public function key(string $name): string
    {
        return 'report:' . md5($name);
    }

    public function remember(string $name, callable $producer)
    {
        return Cache::remember($this->key($name), 60, $producer);
    }
}
