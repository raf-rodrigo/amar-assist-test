<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_complete_category_crud(): void
    {
        $user = User::factory()->create();

        $created = $this->actingAs($user)->postJson('/api/categories', ['description' => 'Moradia'])
            ->assertCreated()
            ->json('id');

        $this->actingAs($user)->getJson('/api/categories?search=Moradia')
            ->assertOk()
            ->assertJsonPath('data.0.description', 'Moradia');

        $this->actingAs($user)->putJson("/api/categories/{$created}", ['description' => 'Casa'])
            ->assertOk()
            ->assertJsonPath('description', 'Casa');

        $this->actingAs($user)->deleteJson("/api/categories/{$created}")->assertNoContent();
        $this->assertDatabaseMissing('categories', ['id' => $created]);
    }

    public function test_user_can_complete_income_crud(): void
    {
        $user = User::factory()->create();
        $category = $user->categories()->create(['description' => 'Trabalho']);
        $payload = ['category_id' => $category->id, 'date' => now()->toDateString(), 'description' => 'Salário', 'amount' => 2500];

        $created = $this->actingAs($user)->postJson('/api/incomes', $payload)->assertCreated()->json('id');
        $this->actingAs($user)->getJson('/api/incomes?search=Salário')->assertOk()->assertJsonPath('data.0.description', 'Salário');
        $this->actingAs($user)->putJson("/api/incomes/{$created}", [...$payload, 'description' => 'Salário atualizado'])
            ->assertOk()->assertJsonPath('description', 'Salário atualizado');
        $this->actingAs($user)->deleteJson("/api/incomes/{$created}")->assertNoContent();
        $this->assertDatabaseMissing('incomes', ['id' => $created]);
    }

    public function test_user_can_complete_expense_crud(): void
    {
        $user = User::factory()->create();
        $category = $user->categories()->create(['description' => 'Moradia']);
        $payload = ['category_id' => $category->id, 'date' => now()->toDateString(), 'description' => 'Aluguel', 'amount' => 1200];

        $created = $this->actingAs($user)->postJson('/api/expenses', $payload)->assertCreated()->json('id');
        $this->actingAs($user)->getJson('/api/expenses?search=Aluguel')->assertOk()->assertJsonPath('data.0.description', 'Aluguel');
        $this->actingAs($user)->putJson("/api/expenses/{$created}", [...$payload, 'description' => 'Aluguel atualizado'])
            ->assertOk()->assertJsonPath('description', 'Aluguel atualizado');
        $this->actingAs($user)->deleteJson("/api/expenses/{$created}")->assertNoContent();
        $this->assertDatabaseMissing('expenses', ['id' => $created]);
    }

    public function test_income_and_expense_lists_can_be_sorted(): void
    {
        $user = User::factory()->create();
        $category = $user->categories()->create(['description' => 'General']);
        $base = ['category_id' => $category->id, 'date' => now()->toDateString()];
        $user->incomes()->create([...$base, 'description' => 'Small income', 'amount' => 100]);
        $user->incomes()->create([...$base, 'description' => 'Large income', 'amount' => 900]);
        $user->expenses()->create([...$base, 'description' => 'Small expense', 'amount' => 50]);
        $user->expenses()->create([...$base, 'description' => 'Large expense', 'amount' => 500]);

        $this->actingAs($user)->getJson('/api/incomes?sort_by=amount&sort_direction=asc')
            ->assertJsonPath('data.0.description', 'Small income');
        $this->actingAs($user)->getJson('/api/expenses?sort_by=amount&sort_direction=desc')
            ->assertJsonPath('data.0.description', 'Large expense');
        $this->actingAs($user)->getJson('/api/incomes?search=900')
            ->assertJsonPath('data.0.description', 'Large income');
        $this->actingAs($user)->getJson('/api/expenses?search=50')
            ->assertJsonFragment(['description' => 'Small expense']);
    }
}
