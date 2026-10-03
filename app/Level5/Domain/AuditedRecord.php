<?php

namespace App\Level5\Domain;

class AuditedRecord extends BaseRecord
{
    protected array $trail = [];

    protected function boot(): void
    {
        $this->trail[] = 'loaded';
    }

    public function trail(): array
    {
        return $this->trail;
    }
}
