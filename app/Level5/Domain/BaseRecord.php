<?php

namespace App\Level5\Domain;

abstract class BaseRecord
{
    protected array $attributes = [];

    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
        $this->boot();
    }

    protected function boot(): void
    {
    }

    public function get(string $key, $default = null)
    {
        return $this->attributes[$key] ?? $default;
    }
}
