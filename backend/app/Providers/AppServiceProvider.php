<?php

namespace App\Providers;

use App\Models\Expense;
use App\Models\Income;
use App\Observers\ExpenseObserver;
use App\Observers\IncomeObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
    }

    public function boot()
    {
        Income::observe(IncomeObserver::class);
        Expense::observe(ExpenseObserver::class);
    }
}
