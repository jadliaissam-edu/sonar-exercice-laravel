<?php

namespace App\Level5\Domain;

class TimestampedRecord extends AuditedRecord
{
    protected ?string $loadedAt = null;

    protected function boot(): void
    {
        parent::boot();
        $this->loadedAt = date('c');
    }

    public function loadedAt(): ?string
    {
        return $this->loadedAt;
    }
}
