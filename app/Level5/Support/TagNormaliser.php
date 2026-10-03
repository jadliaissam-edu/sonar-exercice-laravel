<?php

namespace App\Level5\Support;

class TagNormaliser
{
    public function normalise(array $tags): array
    {
        return array_values(array_unique(array_map([$this, 'clean'], $tags)));
    }

    private function clean($tag)
    {
        return strtolower(trim((string) $tag));
    }
}
