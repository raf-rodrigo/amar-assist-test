<?php

namespace App\Listeners;

use App\Actions\GetMonthlyDashboard;
use App\Events\FinancialEntryChanged;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class ClearDashboardCache
{
    public function handle(FinancialEntryChanged $event): void
    {
        foreach (array_unique($event->months) as $month) {
            Cache::forget(GetMonthlyDashboard::cacheKey($event->userId, Carbon::parse($month)));
        }
    }
}
