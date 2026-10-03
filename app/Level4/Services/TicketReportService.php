<?php

namespace App\Level4\Services;

use App\Models\Ticket;

class TicketReportService
{
    public function buildFullReport(?string $from = null, ?string $to = null): array
    {
        $report = [];

        $report['open_urgent'] = Ticket::where('status', 'open')
            ->where('priority', 'urgent')
            ->when($from, fn ($q) => $q->where('created_at', '>=', $from))
            ->when($to, fn ($q) => $q->where('created_at', '<=', $to))
            ->count();

        $report['open_high'] = Ticket::where('status', 'open')
            ->where('priority', 'high')
            ->when($from, fn ($q) => $q->where('created_at', '>=', $from))
            ->when($to, fn ($q) => $q->where('created_at', '<=', $to))
            ->count();

        $report['open_normal'] = Ticket::where('status', 'open')
            ->where('priority', 'normal')
            ->when($from, fn ($q) => $q->where('created_at', '>=', $from))
            ->when($to, fn ($q) => $q->where('created_at', '<=', $to))
            ->count();

        $report['open_low'] = Ticket::where('status', 'open')
            ->where('priority', 'low')
            ->when($from, fn ($q) => $q->where('created_at', '>=', $from))
            ->when($to, fn ($q) => $q->where('created_at', '<=', $to))
            ->count();

        $report['pending_urgent'] = Ticket::where('status', 'pending')
            ->where('priority', 'urgent')
            ->when($from, fn ($q) => $q->where('created_at', '>=', $from))
            ->when($to, fn ($q) => $q->where('created_at', '<=', $to))
            ->count();

        $report['pending_high'] = Ticket::where('status', 'pending')
            ->where('priority', 'high')
            ->when($from, fn ($q) => $q->where('created_at', '>=', $from))
            ->when($to, fn ($q) => $q->where('created_at', '<=', $to))
            ->count();

        $report['pending_normal'] = Ticket::where('status', 'pending')
            ->where('priority', 'normal')
            ->when($from, fn ($q) => $q->where('created_at', '>=', $from))
            ->when($to, fn ($q) => $q->where('created_at', '<=', $to))
            ->count();

        $report['pending_low'] = Ticket::where('status', 'pending')
            ->where('priority', 'low')
            ->when($from, fn ($q) => $q->where('created_at', '>=', $from))
            ->when($to, fn ($q) => $q->where('created_at', '<=', $to))
            ->count();

        $report['closed_urgent'] = Ticket::where('status', 'closed')
            ->where('priority', 'urgent')
            ->when($from, fn ($q) => $q->where('created_at', '>=', $from))
            ->when($to, fn ($q) => $q->where('created_at', '<=', $to))
            ->count();

        $report['closed_high'] = Ticket::where('status', 'closed')
            ->where('priority', 'high')
            ->when($from, fn ($q) => $q->where('created_at', '>=', $from))
            ->when($to, fn ($q) => $q->where('created_at', '<=', $to))
            ->count();

        $report['closed_normal'] = Ticket::where('status', 'closed')
            ->where('priority', 'normal')
            ->when($from, fn ($q) => $q->where('created_at', '>=', $from))
            ->when($to, fn ($q) => $q->where('created_at', '<=', $to))
            ->count();

        $report['closed_low'] = Ticket::where('status', 'closed')
            ->where('priority', 'low')
            ->when($from, fn ($q) => $q->where('created_at', '>=', $from))
            ->when($to, fn ($q) => $q->where('created_at', '<=', $to))
            ->count();

        $report['urgent_unassigned'] = Ticket::where('priority', 'urgent')
            ->whereNull('assignee_id')
            ->when($from, fn ($q) => $q->where('created_at', '>=', $from))
            ->when($to, fn ($q) => $q->where('created_at', '<=', $to))
            ->count();

        $report['high_unassigned'] = Ticket::where('priority', 'high')
            ->whereNull('assignee_id')
            ->when($from, fn ($q) => $q->where('created_at', '>=', $from))
            ->when($to, fn ($q) => $q->where('created_at', '<=', $to))
            ->count();

        $report['normal_unassigned'] = Ticket::where('priority', 'normal')
            ->whereNull('assignee_id')
            ->when($from, fn ($q) => $q->where('created_at', '>=', $from))
            ->when($to, fn ($q) => $q->where('created_at', '<=', $to))
            ->count();

        $report['low_unassigned'] = Ticket::where('priority', 'low')
            ->whereNull('assignee_id')
            ->when($from, fn ($q) => $q->where('created_at', '>=', $from))
            ->when($to, fn ($q) => $q->where('created_at', '<=', $to))
            ->count();

        $report['urgent_overdue'] = Ticket::where('priority', 'urgent')
            ->where('status', '!=', 'closed')
            ->where('due_at', '<', now())
            ->when($from, fn ($q) => $q->where('created_at', '>=', $from))
            ->when($to, fn ($q) => $q->where('created_at', '<=', $to))
            ->count();

        $report['high_overdue'] = Ticket::where('priority', 'high')
            ->where('status', '!=', 'closed')
            ->where('due_at', '<', now())
            ->when($from, fn ($q) => $q->where('created_at', '>=', $from))
            ->when($to, fn ($q) => $q->where('created_at', '<=', $to))
            ->count();

        $report['normal_overdue'] = Ticket::where('priority', 'normal')
            ->where('status', '!=', 'closed')
            ->where('due_at', '<', now())
            ->when($from, fn ($q) => $q->where('created_at', '>=', $from))
            ->when($to, fn ($q) => $q->where('created_at', '<=', $to))
            ->count();

        $report['low_overdue'] = Ticket::where('priority', 'low')
            ->where('status', '!=', 'closed')
            ->where('due_at', '<', now())
            ->when($from, fn ($q) => $q->where('created_at', '>=', $from))
            ->when($to, fn ($q) => $q->where('created_at', '<=', $to))
            ->count();

        $report['total'] = 0;
        foreach ($report as $key => $value) {
            if (substr($key, -11) !== '_unassigned' && substr($key, -8) !== '_overdue') {
                $report['total'] += $value;
            }
        }

        return $report;
    }

    public function renderRow(
        $id,
        $title,
        $status,
        $priority,
        $assignee,
        $category,
        $createdAt,
        $dueAt,
        $format = 'csv'
    ) {
        $cells = [$id, $title, $status, $priority, $assignee, $category, $createdAt, $dueAt];

        if ($format === 'csv') {
            return implode(',', $cells);
        }

        return json_encode($cells);
    }
}
