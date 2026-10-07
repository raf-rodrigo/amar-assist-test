<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_sums_only_current_users_current_month_entries(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $category = $user->categories()->create(['description' => 'General']);
        $otherCategory = $other->categories()->create(['description' => 'General']);

        $user->incomes()->create(['category_id' => $category->id, 'date' => now(), 'description' => 'Income', 'amount' => 500]);
        $user->expenses()->create(['category_id' => $category->id, 'date' => now(), 'description' => 'Expense', 'amount' => 125.50]);
        $other->incomes()->create(['category_id' => $otherCategory->id, 'date' => now(), 'description' => 'Other', 'amount' => 999]);

        $this->actingAs($user)->getJson('/api/dashboard')->assertOk()->assertJson([
            'income' => '500.00', 'expense' => '125.50', 'balance' => '374.50',
        ]);
    }

    public function test_financial_entry_observer_clears_dashboard_cache(): void
    {
        $user = User::factory()->create();
        $category = $user->categories()->create(['description' => 'General']);

        $this->actingAs($user)->getJson('/api/dashboard')->assertJsonPath('income', '0.00');
        $user->incomes()->create([
            'category_id' => $category->id,
            'date' => now()->toDateString(),
            'description' => 'Income after cache',
            'amount' => 300,
        ]);

        $this->actingAs($user)->getJson('/api/dashboard')->assertJsonPath('income', '300.00');
    }
}
