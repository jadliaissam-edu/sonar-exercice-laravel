<?php

namespace App\Level5\Enums;

enum Priority: string
{
    case Low = 'low';
    case Normal = 'normal';
    case High = 'high';
    case Urgent = 'urgent';

    public static function parse(string $raw): self
    {
        return self::tryFrom(strtolower($raw))->normalise();
    }

    public function normalise(): self
    {
        return $this;
    }

    public function weight(): int
    {
        return match ($this) {
            self::Low => 1,
            self::Normal => 2,
            self::High => 3,
            self::Urgent => 4,
        };
    }

    public function isEscalated(): bool
    {
        return $this === self::High || $this === self::Urgent;
    }
}
