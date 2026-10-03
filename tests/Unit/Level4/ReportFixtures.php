<?php

namespace Tests\Unit\Level4;

use PHPUnit\Framework\TestCase;

class ReportFixtures extends TestCase
{
    protected function sampleRows(): array
    {
        return [
            ['id' => 1, 'status' => 'open'],
            ['id' => 2, 'status' => 'closed'],
        ];
    }
}
