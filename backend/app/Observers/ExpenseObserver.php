<?php

namespace App\Observers;

use App\Events\FinancialEntryChanged;
use App\Models\Expense;

class ExpenseObserver
{
    public function saved(Expense $expense): void
    {
        $this->dispatchChange($expense);
    }

    public function deleted(Expense $expense): void
    {
        $this->dispatchChange($expense);
    }

    private function dispatchChange(Expense $expense): void
    {
        FinancialEntryChanged::dispatch($expense->user_id, array_filter([
            optional($expense->date)->toDateString(),
            $expense->getOriginal('date'),
        ]));
    }
}
