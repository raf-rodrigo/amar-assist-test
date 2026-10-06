<?php

namespace App\Jobs;

use App\Mail\TomorrowExpensesMail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendTomorrowExpenseReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $userId, public string $date) {}

    public function handle(): void
    {
        $user = User::find($this->userId);
        if (! $user) return;

        $expenses = $user->expenses()
            ->with('category')
            ->whereDate('date', $this->date)
            ->orderBy('description')
            ->get();

        if ($expenses->isNotEmpty()) {
            Mail::to($user)->send(new TomorrowExpensesMail($user, $expenses, $this->date));
        }
    }
}
