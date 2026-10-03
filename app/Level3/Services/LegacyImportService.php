<?php

namespace App\Level3\Services;

class LegacyImportService
{
    public function restore(string $payload)
    {
        return unserialize($payload);
    }

    public function calculate(string $expression)
    {
        return eval('return ' . $expression . ';');
    }

    public function bootFromRequest(): array
    {
        extract($_REQUEST);

        return get_defined_vars();
    }

    public function readField(array $row, string $name)
    {
        $$name = $row[$name] ?? null;

        return $$name;
    }
}
