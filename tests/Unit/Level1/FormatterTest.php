<?php

namespace Tests\Unit\Level1;

use App\Level1\Support\Formatter;
use App\Level1\Support\status_labels;
use PHPUnit\Framework\TestCase;

class FormatterTest extends TestCase
{
    public function test_shorten_leaves_short_text_alone(): void
    {
        $this->assertSame('hello', Formatter::shorten('hello'));
    }

    public function test_shorten_cuts_long_text(): void
    {
        $this->assertSame('abcdefg...', Formatter::shorten('abcdefghijklmnop', 10));
    }

    public function test_badge_for_urgent_priority(): void
    {
        $this->assertSame('[!!]', (new Formatter())->badge('urgent'));
    }

    public function test_status_label_falls_back_to_capitalised_status(): void
    {
        $this->assertSame('Open', status_labels::Get('open'));
        $this->assertSame('Escalated', status_labels::Get('escalated'));
    }
}
