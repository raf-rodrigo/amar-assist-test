<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_view_update_or_delete_another_users_income(): void
    {
        $owner = User::factory()->create();
        $attacker = User::factory()->create();
        $category = $owner->categories()->create(['description' => 'Salary']);
        $income = $owner->incomes()->create([
            'category_id' => $category->id,
            'date' => now()->toDateString(),
            'description' => 'Private income',
            'amount' => 100,
        ]);

        $this->actingAs($attacker)->getJson("/api/incomes/{$income->id}")->assertNotFound();
        $this->actingAs($attacker)->deleteJson("/api/incomes/{$income->id}")->assertNotFound();
    }

    public function test_user_cannot_assign_another_users_category(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $category = $other->categories()->create(['description' => 'Private']);

        $this->actingAs($user)->postJson('/api/incomes', [
            'category_id' => $category->id,
            'date' => now()->toDateString(),
            'description' => 'Attempt',
            'amount' => 10,
        ])->assertUnprocessable()->assertJsonValidationErrors('category_id');
    }
}

