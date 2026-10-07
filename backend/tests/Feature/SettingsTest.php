<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_reminder_time(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->patchJson('/api/settings', ['reminder_time' => '08:20'])
            ->assertOk()
            ->assertJsonPath('reminder_time', '08:20');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'reminder_time' => '08:20',
        ]);
    }
}
