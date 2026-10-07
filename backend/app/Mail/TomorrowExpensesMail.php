<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class TomorrowExpensesMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Collection $expenses,
        public string $dueDate
    ) {}

    public function build(): self
    {
        return $this
            ->subject('Lembrete: despesas vencendo amanhã')
            ->view('emails.tomorrow-expenses')
            ->text('emails.tomorrow-expenses-text');
    }
}
