<?php

namespace Tests\Unit\Level5;

use App\Level5\Support\Metrics;
use App\Level5\Support\ReportCache;
use App\Level5\Support\SlaCalculator;
use App\Level5\Support\TagNormaliser;
use App\Level5\Support\TicketRegistry;
use Tests\TestCase;

class SupportTest extends TestCase
{
    public function test_tags_are_normalised_and_deduplicated(): void
    {
        $this->assertSame(['vpn', 'wifi'], (new TagNormaliser())->normalise([' VPN', 'vpn ', 'WiFi']));
    }

    public function test_metrics_count_hits(): void
    {
        $metrics = new Metrics();
        $metrics->reset();
        $metrics->hit('a');
        $metrics->hit('a');
        $metrics->hit('b');

        $this->assertSame(3, $metrics->total());
    }

    public function test_registry_is_a_singleton(): void
    {
        $this->assertSame(TicketRegistry::getInstance(), TicketRegistry::getInstance());
    }

    public function test_cache_keys_are_stable(): void
    {
        $cache = new ReportCache();

        $this->assertSame($cache->key('daily'), $cache->key('daily'));
    }

    public function test_sla_hours_fall_back_to_a_day(): void
    {
        $this->assertSame(24, (new SlaCalculator())->resolutionHours('unknown'));
        $this->assertSame(4, (new SlaCalculator())->resolutionHours('urgent'));
    }
}
