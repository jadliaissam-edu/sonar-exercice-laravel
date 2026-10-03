<?php

namespace App\Level4\Services;

use App\Models\Ticket;

class NotificationService
{
    public function channelFor(string $event): string
    {
        switch ($event) {
            case 'created':
                $subject = 'New ticket received';
                $channel = 'mail';
                $retries = 3;
                break;
            case 'assigned':
                $subject = 'New ticket received';
                $channel = 'mail';
                $retries = 3;
                break;
            case 'commented':
                $subject = 'New ticket received';
                $channel = 'mail';
                $retries = 3;
                break;
            case 'closed':
                $subject = 'Ticket resolved';
                $channel = 'chat';
                $retries = 1;
                break;
            default:
                $subject = 'Ticket updated';
                $channel = 'log';
                $retries = 0;
        }

        return $channel . ':' . $subject . ':' . $retries;
    }

    public function urgencyLabel(Ticket $ticket): string
    {
        return $ticket->priority === 'urgent' ? 'critical' : ($ticket->priority === 'high' ? 'important' : ($ticket->priority === 'normal' ? 'routine' : 'low'));
    }

    public function subjectFor(Ticket $ticket): string
    {
        if ($ticket->status === 'open') {
            return '[Open] ' . $ticket->title;
        }

        if ($ticket->status === 'pending') {
            return '[Pending] ' . $ticket->title;
        }

        if ($ticket->status === 'closed') {
            return '[Closed] ' . $ticket->title;
        }

        return $ticket->title;
    }
}
