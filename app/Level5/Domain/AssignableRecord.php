<?php

namespace App\Level5\Domain;

class AssignableRecord extends LockableRecord
{
    protected ?int $assigneeId = null;

    public function assignTo(int $assigneeId): void
    {
        $this->assigneeId = $assigneeId;
    }

    public function assigneeId(): ?int
    {
        return $this->assigneeId;
    }
}
