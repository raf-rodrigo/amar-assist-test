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
}
