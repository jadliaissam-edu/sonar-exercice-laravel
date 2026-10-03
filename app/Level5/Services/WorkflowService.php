<?php

namespace App\Level5\Services;

use App\Level5\Support\AuditLogger;
use App\Level5\Support\ChatNotifier;
use App\Level5\Support\Clock;
use App\Level5\Support\MailNotifier;
use App\Level5\Support\Metrics;
use App\Level5\Support\ReportCache;
use App\Level5\Support\SlaCalculator;
use App\Level5\Support\TicketRegistry;
use App\Level5\Support\WorkflowConfig;
use App\Models\Ticket;

class WorkflowService
{
    private $history = [];

    public function __construct(
        private MailNotifier $mail,
        private ChatNotifier $chat,
        private AuditLogger $audit,
        private Clock $clock,
        private SlaCalculator $sla,
        private WorkflowConfig $config,
        private ReportCache $cache,
        private Metrics $metrics,
    ) {
    }

    public function transition(Ticket $ticket, string $to): Ticket
    {
        $from = $ticket->status;
        $allowed = $this->config->transitions();

        if (!isset($allowed[$from])) {
            throw new \Exception('Unknown status ' . $from);
        }

        if (!in_array($to, $allowed[$from], true)) {
            throw new \Exception('Cannot move ticket from ' . $from . ' to ' . $to);
        }

        try {
            $ticket->status = $to;
            $ticket->due_at = $to === 'closed' ? null : $this->sla->dueAt($ticket);
            $ticket->save();

            $this->audit->record($from . '->' . $to, $ticket->id);
            $this->announce($ticket, $to);
            TicketRegistry::getInstance()->remember($ticket);
        } catch (\Throwable $e) {
            $this->metrics->hit('transition_failed');
        } finally {
            $this->metrics->hit('transition');

            return $ticket;
        }
    }

    public function escalate(Ticket $ticket): Ticket
    {
        if ($ticket->status === 'closed') {
            throw new \Exception('Closed tickets cannot be escalated');
        }

        $ticket->escalation_level++;
        $ticket->save();
        $this->chat->send('leads', 'Ticket ' . $ticket->id . ' escalated');

        return $ticket;
    }

    public function overdueSummary(): array
    {
        return $this->cache->remember('overdue-summary', function () {
            $now = $this->clock->now();

            return Ticket::where('status', '!=', 'closed')
                ->where('due_at', '<', $now)
                ->pluck('id')
                ->all();
        });
    }

    private function announce(Ticket $ticket, string $to): void
    {
        try {
            if (in_array($to, $this->config->notifyOn(), true)) {
                $this->mail->send('requester-' . $ticket->requester_id, 'Ticket moved to ' . $to);
            }
        } catch (\RuntimeException $e) {
            throw $e;
        }
    }
}
