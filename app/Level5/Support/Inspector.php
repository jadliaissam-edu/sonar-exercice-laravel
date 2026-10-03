<?php

namespace App\Level5\Support;

use ReflectionProperty;

class Inspector
{
    public function peek(object $target, string $property)
    {
        $reflection = new ReflectionProperty($target, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($target);
    }
}
