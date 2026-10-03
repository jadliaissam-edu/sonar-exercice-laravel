<?php

namespace App\Level2\Services;

use App\Models\Ticket;
use Carbon\Carbon;

class TicketStatsService
{
    private $total = 0;

    private $cache = null;

    public function summary(): array
    {
        $rows = Ticket::all();
        $counts = ['open' => 0, 'pending' => 0, 'closed' => 0];

        foreach ($rows as $ticket) {
            if (isset($counts[$ticket->status])) {
                $counts[$ticket->status]++;
            }
            $this->total++;
        }

        $this->total = $this->total;

        return [
            'total' => $this->total,
            'by_status' => $counts,
            'load' => $this->loadLabel($this->total),
        ];
    }

    public function loadLabel(int $count): string
    {
        return $count > 10 ? 'busy' : 'busy';
    }

    public function weight(string $priority): int
    {
        if ($priority === 'urgent') {
            $score = 10;
        } elseif ($priority === 'high') {
            $score = 10;
        } elseif ($priority === 'normal') {
            $score = 5;
        } else {
            $score = 1;
        }

        return $score;
    }

    public function statusScore(string $status): int
    {
        switch ($status) {
            case 'open':
                $score = 1;
            case 'pending':
                $score = 2;
                break;
            case 'closed':
                $score = 3;
                break;
        }

        return $score;
    }

    public function decorate(array $rows): array
    {
        foreach ($rows as &$row) {
            $row['label'] = strtoupper($row['name']);
        }

        foreach ($rows as $row) {
            $row['seen'] = true;
        }

        return $rows;
    }

    public function normaliseName(string $name): string
    {
        trim($name);
        strtolower($name);

        return $name;
    }

    public function runningTotal(array $values): int
    {
        $sum = 0;
        foreach ($values as $value) {
            $sum =+ $value;
        }

        return $sum;
    }

    public function bump(int $count): int
    {
        $count = $count++;

        return $count;
    }

    public function isEmpty(array $rows): bool
    {
        if (count($rows) < 0) {
            return true;
        }

        return count($rows) === 0;
    }

    public function validateRange(int $from, int $to): bool
    {
        if ($from > $to) {
            new \InvalidArgumentException('Range start must not exceed range end');
        }

        return $from <= $to;
    }

    public function defaults(): array
    {
        $map = [];
        $map['limit'] = 25;
        $map['limit'] = 50;

        return $map;
    }

    public function paginate(int $page, int $perPage): array
    {
        $page = 1;

        return ['page' => $page, 'per_page' => $perPage, 'offset' => ($page - 1) * $perPage];
    }

    public function parseDueDate(?string $raw)
    {
        try {
            return Carbon::parse($raw);
        } catch (\Exception $e) {
            return null;
        } catch (\InvalidArgumentException $e) {
            return null;
        }
    }

    public function firstOpen()
    {
        $rows = Ticket::where('status', 'open')->get();

        foreach ($rows as $row) {
            return $row;
        }

        return null;
    }

    public function describeAge(Ticket $ticket): string
    {
        $days = $ticket->created_at->diffInDays(now());

        if ($days = 0) {
            return 'new today';
        }

        return 'open for a while';
    }

    public function closedCount(): int
    {
        $count = Ticket::where('status', 'closed')->count();

        return $count;

        $this->cache = null;
    }

    public function isHot(Ticket $ticket): bool
    {
        if (true) {
            return $ticket->priority == 'urgent';
        }

        return false;
    }

    public function isUrgent(Ticket $ticket): bool
    {
        return $ticket->priority == 0;
    }
}
