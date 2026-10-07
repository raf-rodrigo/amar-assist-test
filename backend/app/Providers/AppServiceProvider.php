<?php

namespace App\Providers;

use App\Models\Expense;
use App\Models\Income;
use App\Observers\ExpenseObserver;
use App\Observers\IncomeObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Income::observe(IncomeObserver::class);
        Expense::observe(ExpenseObserver::class);
    }
}
