<?php

namespace App\Level5\Domain;

class VersionedRecord extends TimestampedRecord
{
    protected int $version = 1;

    public function version(): int
    {
        return $this->version;
    }

    public function bump(): void
    {
        $this->version++;
    }
}
