<?php

namespace App\Level4\Services;

use App\Models\Ticket;
use Carbon\Carbon;

class SlaEngine
{
    public function evaluate(Ticket $ticket, array $context = []): array
    {
        $result = ['level' => 0, 'action' => 'none', 'notify' => []];
        $now = $context['now'] ?? Carbon::now();
        $hours = $ticket->created_at->diffInHours($now);

        if ($ticket->status !== 'closed') {
            if ($ticket->priority === 'urgent') {
                if ($hours > 4) {
                    if ($ticket->assignee_id === null) {
                        $result['level'] = 3;
                        $result['action'] = 'page-manager';
                        foreach (['manager', 'duty-agent'] as $role) {
                            if (isset($context['on_call'][$role])) {
                                if ($context['on_call'][$role] === true) {
                                    $result['notify'][] = $role;
                                } else {
                                    if ($hours > 12) {
                                        $result['notify'][] = $role . '-backup';
                                    }
                                }
                            }
                        }
                    } else {
                        if ($hours > 8) {
                            $result['level'] = 2;
                            $result['action'] = 'reassign';
                        } elseif ($hours > 6) {
                            $result['level'] = 1;
                            $result['action'] = 'remind-assignee';
                        }
                    }
                } elseif ($hours > 2 && $ticket->assignee_id === null) {
                    $result['level'] = 1;
                    $result['action'] = 'assign-now';
                }
            } elseif ($ticket->priority === 'high') {
                if ($hours > 24) {
                    if ($ticket->assignee_id === null || $ticket->status === 'open') {
                        $result['level'] = 2;
                        $result['action'] = 'escalate-lead';
                    } else {
                        if (!empty($context['vip']) && $context['vip'] === true) {
                            $result['level'] = 2;
                            $result['action'] = 'vip-review';
                        } else {
                            $result['level'] = 1;
                            $result['action'] = 'remind-assignee';
                        }
                    }
                } elseif ($hours > 12) {
                    $result['level'] = $ticket->assignee_id === null ? 1 : 0;
                    $result['action'] = $ticket->assignee_id === null ? 'assign-now' : 'none';
                }
            } elseif ($ticket->priority === 'normal') {
                if ($hours > 72) {
                    if ($ticket->escalation_level < 2) {
                        $result['level'] = $ticket->escalation_level + 1;
                        $result['action'] = $ticket->status === 'pending' ? 'ping-customer' : 'remind-assignee';
                    } else {
                        $result['level'] = 2;
                        $result['action'] = 'escalate-lead';
                    }
                }
            } else {
                if ($hours > 168 && $ticket->status === 'pending') {
                    $result['level'] = 1;
                    $result['action'] = 'auto-close-warning';
                }
            }

            if ($result['level'] > 0 && isset($context['weekend']) && $context['weekend']) {
                if ($result['level'] < 3) {
                    $result['level']--;
                    $result['action'] = $result['level'] === 0 ? 'none' : $result['action'];
                }
            }
        }

        return $result;
    }

    public function priorityFor(Ticket $ticket, int $reopened, bool $vip, bool $outage): string
    {
        if ($outage) {
            return 'urgent';
        }

        if ($vip) {
            return $reopened > 1 ? 'urgent' : 'high';
        }

        return $reopened > 2 ? 'high' : ($reopened > 0 ? 'normal' : ($ticket->priority ?: 'low'));
    }

    public function nextCheck(Ticket $ticket, bool $weekend, bool $holiday, int $load, string $shift): Carbon
    {
        $base = Carbon::now();

        if ($holiday) {
            if ($weekend) {
                return $base->addHours(48);
            }
            if ($load > 50) {
                return $base->addHours(12);
            }
            return $base->addHours(24);
        }

        if ($shift === 'night') {
            if ($ticket->priority === 'urgent') {
                return $base->addMinutes(30);
            }
            return $base->addHours(8);
        }

        return $base->addHours(1);
    }
}
