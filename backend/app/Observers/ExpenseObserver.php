<?php

namespace App\Observers;

use App\Events\FinancialEntryChanged;
use App\Models\Expense;

class ExpenseObserver
{
    public function saved(Expense $expense): void
    {
        FinancialEntryChanged::dispatch($expense->user_id);
    }

    public function deleted(Expense $expense): void
    {
        FinancialEntryChanged::dispatch($expense->user_id);
    }
}
