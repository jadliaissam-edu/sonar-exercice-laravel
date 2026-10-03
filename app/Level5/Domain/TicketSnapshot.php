<?php

namespace App\Level5\Domain;

final readonly class TicketSnapshot
{
    public function __construct(
        public int $id,
        public string $title,
        public array $tags = [],
    ) {
    }

    public function withTag(string $tag): static
    {
        $copy = clone $this;
        $copy->tags[] = $tag;

        return $copy;
    }

    public function label(): string
    {
        return '#' . $this->id . ' ' . $this->title;
    }
}
