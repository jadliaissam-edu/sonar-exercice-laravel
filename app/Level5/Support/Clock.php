<?php

namespace App\Level5\Support;

use Carbon\Carbon;

class Clock
{
    public function now(): Carbon
    {
        return Carbon::now();
    }
}
