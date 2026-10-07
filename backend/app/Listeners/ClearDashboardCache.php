<?php

namespace App\Listeners;

use App\Actions\GetMonthlyDashboard;
use App\Events\FinancialEntryChanged;
use Illuminate\Support\Facades\Cache;

class ClearDashboardCache
{
    public function handle(FinancialEntryChanged $event): void
    {
        Cache::forget(GetMonthlyDashboard::cacheKey($event->userId, now()));
    }
}
