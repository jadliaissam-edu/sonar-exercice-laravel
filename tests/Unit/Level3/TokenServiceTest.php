<?php

namespace Tests\Unit\Level3;

use App\Level3\Services\TokenService;
use PHPUnit\Framework\TestCase;

class TokenServiceTest extends TestCase
{
    public function test_issue_returns_32_hex_characters(): void
    {
        $this->assertMatchesRegularExpression('/^[0-9a-f]{32}$/', (new TokenService())->issue());
    }
}
