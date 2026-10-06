<?php

namespace App\Console\Commands;

use App\Jobs\SendTomorrowExpenseReminder;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class DispatchTomorrowExpenseReminders extends Command
{
    protected $signature = 'expenses:dispatch-reminders';
    protected $description = 'Queue e-mail reminders for expenses due tomorrow';

    public function handle(): int
    {
        $tomorrow = now()->addDay()->toDateString();
        User::query()
            ->whereHas('expenses', fn (Builder $query) => $query->whereDate('date', $tomorrow))
            ->eachById(fn (User $user) => SendTomorrowExpenseReminder::dispatch($user->id, $tomorrow));

        return self::SUCCESS;
    }
}

