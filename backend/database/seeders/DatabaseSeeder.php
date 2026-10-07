<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $users = [
            ['name' => 'Usuário Demonstração', 'email' => 'demo@example.com'],
            ['name' => 'Ana Souza', 'email' => 'ana@example.com'],
            ['name' => 'Bruno Lima', 'email' => 'bruno@example.com'],
        ];

        foreach ($users as $data) {
            \App\Models\User::updateOrCreate(
                ['email' => $data['email']],
                ['name' => $data['name'], 'password' => bcrypt('password'), 'email_verified_at' => now()]
            );
        }
    }
}
