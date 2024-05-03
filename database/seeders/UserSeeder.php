<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory(10)->create();

        User::factory()->create([
            'first_name' => 'User',
            'last_name' => 'Test',
            'is_admin' => true,
            'email' => 'test@example.com',
        ]);
    }
}
