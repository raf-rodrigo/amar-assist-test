<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialDateValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_income_rejects_past_date_and_date_after_current_month(): void
    {
        Carbon::setTestNow('2026-10-06 10:00:00');
        $user = User::factory()->create();
        $category = $user->categories()->create(['description' => 'Salary']);
        $payload = ['category_id' => $category->id, 'description' => 'Salary', 'amount' => 100];

        $this->actingAs($user)->postJson('/api/incomes', [...$payload, 'date' => '2026-10-05'])
            ->assertUnprocessable()->assertJsonValidationErrors('date');
        $this->actingAs($user)->postJson('/api/incomes', [...$payload, 'date' => '2026-11-01'])
            ->assertUnprocessable()->assertJsonValidationErrors('date');
    }

    public function test_expense_rejects_past_date_and_date_over_twelve_months(): void
    {
        Carbon::setTestNow('2026-10-06 10:00:00');
        $user = User::factory()->create();
        $category = $user->categories()->create(['description' => 'Housing']);
        $payload = ['category_id' => $category->id, 'description' => 'Rent', 'amount' => 100];

        $this->actingAs($user)->postJson('/api/expenses', [...$payload, 'date' => '2026-10-05'])
            ->assertUnprocessable()->assertJsonValidationErrors('date');
        $this->actingAs($user)->postJson('/api/expenses', [...$payload, 'date' => '2027-10-07'])
            ->assertUnprocessable()->assertJsonValidationErrors('date');
    }
}

