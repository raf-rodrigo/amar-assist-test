<?php

namespace App\Console\Commands;

use App\Jobs\SendTomorrowExpenseReminder;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class DispatchTomorrowExpenseReminders extends Command
{
    /** Comando responsável por colocar na fila os avisos de vencimento. */
    protected $signature = 'expenses:dispatch-reminders';
    protected $description = 'Adiciona à fila os avisos de despesas com vencimento amanhã';

    public function handle(): int
    {
        $tomorrow = now()->addDay()->toDateString();
        $reminderTime = now()->format('H:i');
        User::query()
            ->whereRaw('CAST(reminder_time AS TEXT) LIKE ?', ["{$reminderTime}%"])
            ->whereHas('expenses', fn (Builder $query) => $query->whereDate('date', $tomorrow))
            ->eachById(fn (User $user) => SendTomorrowExpenseReminder::dispatch($user->id, $tomorrow));

        return self::SUCCESS;
    }
}
