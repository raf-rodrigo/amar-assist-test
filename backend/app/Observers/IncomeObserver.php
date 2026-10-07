<?php

namespace App\Observers;

use App\Events\FinancialEntryChanged;
use App\Models\Income;

class IncomeObserver
{
    public function saved(Income $income): void
    {
        $this->dispatchChange($income);
    }

    public function deleted(Income $income): void
    {
        $this->dispatchChange($income);
    }

    private function dispatchChange(Income $income): void
    {
        FinancialEntryChanged::dispatch($income->user_id, array_filter([
            optional($income->date)->toDateString(),
            $income->getOriginal('date'),
        ]));
    }
}
