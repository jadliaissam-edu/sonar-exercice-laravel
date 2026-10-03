<?php

namespace App\Level5\Support;

class WorkflowConfig
{
    public function transitions(): array
    {
        return [
            'open' => ['pending', 'closed'],
            'pending' => ['open', 'closed'],
            'closed' => ['open'],
        ];
    }

    public function notifyOn(): array
    {
        return ['closed', 'pending'];
    }
}
