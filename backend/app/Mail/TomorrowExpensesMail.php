<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class TomorrowExpensesMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Collection $expenses, public string $dueDate) {}

    public function build(): self
    {
        return $this->subject('Despesas que vencem amanhã')->view('emails.tomorrow-expenses');
    }
}
