<?php

namespace App\Observers;

use App\Events\FinancialEntryChanged;
use App\Models\Income;

class IncomeObserver
{
    public function saved(Income $income): void
    {
        FinancialEntryChanged::dispatch($income->user_id);
    }

    public function deleted(Income $income): void
    {
        FinancialEntryChanged::dispatch($income->user_id);
    }
}
