<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProfileService
{
    public function getProfile(int $userId): array
    {
        $user = User::with(['contact', 'name', 'role'])->findOrFail($userId);

        return [
            'login' => $user->login,
            'role' => $user->role->title,
            'name' => $user->name->name,
            'surname' => $user->name->surname,
            'patronymic' => $user->name->patronymic,
            'phone' => $user->contact->phone,
            'email' => $user->contact->email,
        ];
    }

    public function updateProfile(int $userId, array $data): void
    {
        $user = User::with(['contact', 'name'])->findOrFail($userId);

        DB::transaction(function () use ($user, $data) {
            $user->name->update([
                'name' => $data['name'],
                'surname' => $data['surname'],
                'patronymic' => $data['patronymic'] ?? null,
            ]);

            $user->contact->update([
                'phone' => $data['phone'],
                'email' => $data['email'],
            ]);
        });
    }

    public function getUserTickets(int $userId): array
    {
        $now = Carbon::now();

        $tickets = Ticket::with(['trip.route', 'trip.train'])
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->get();

        return [
            'active' => $tickets->filter(fn($t) =>
                !$t->is_canceled &&
                $t->trip->start_timestamp > $now &&
                $t->booking->status === 'PAID'
            )->values(),
            'past' => $tickets->filter(fn($t) =>
                !$t->is_canceled &&
                $t->trip->end_timestamp < $now &&
                $t->booking->status === 'PAID'
            )->values(),
            'canceled' => $tickets->filter(fn($t) => $t->is_canceled)->values(),
        ];
    }

    public function cancelTicket(int $userId, int $ticketId): bool
    {
        $ticket = Ticket::where('user_id', $userId)
            ->where('is_canceled', false)
            ->find($ticketId);

        if (!$ticket) {
            return false;
        }

        // Можно добавить логику возврата средств и истории операций
        $ticket->update(['is_canceled' => true]);
        return true;
    }
}
