<?php

namespace App\Actions;

use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Cache;

class GetMonthlyDashboard
{
    public function execute(User $user, CarbonInterface $month): array
    {
        $key = self::cacheKey($user->id, $month);

        return Cache::remember($key, now()->addMinutes(10), function () use ($user, $month): array {
            $range = [$month->copy()->startOfMonth()->toDateString(), $month->copy()->endOfMonth()->toDateString()];
            $income = (string) $user->incomes()->whereBetween('date', $range)->sum('amount');
            $expense = (string) $user->expenses()->whereBetween('date', $range)->sum('amount');

            return [
                'month' => $month->format('Y-m'),
                'income' => number_format((float) $income, 2, '.', ''),
                'expense' => number_format((float) $expense, 2, '.', ''),
                'balance' => number_format((float) $income - (float) $expense, 2, '.', ''),
            ];
        });
    }

    public static function cacheKey(int $userId, CarbonInterface $month): string
    {
        return "dashboard:{$userId}:{$month->format('Y-m')}";
    }
}
