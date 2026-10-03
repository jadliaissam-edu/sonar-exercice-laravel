<?php

namespace App\Level1\Support;

class status_labels
{
    const LABELS = [
        'open' => 'Open',
        'pending' => 'Waiting for customer',
        'closed' => 'Closed',
    ];

    public static function Get($status)
    {
        if (isset(self::LABELS[$status])) {
            return self::LABELS[$status];
        }

        return ucfirst((string) $status);
    }
}