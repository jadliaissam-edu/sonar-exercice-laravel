<?php

namespace App\Level5\Domain;

class LockableRecord extends VersionedRecord
{
    protected bool $locked = false;

    public function lock(): void
    {
        $this->locked = true;
    }

    public function isLocked(): bool
    {
        return $this->locked;
    }
}
