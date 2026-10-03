<?php

namespace Tests\Unit\Level4;

use App\Level4\Services\TicketReportService;
use PHPUnit\Framework\TestCase;

class ReportRowChecks extends TestCase
{
    public function test_row_is_rendered_as_csv(): void
    {
        $service = new TicketReportService();

        $this->assertSame('1,a,open,low,,,,', $service->renderRow(1, 'a', 'open', 'low', '', '', '', ''));
    }
}
