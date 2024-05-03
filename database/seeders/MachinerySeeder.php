<?php

namespace Database\Seeders;

use App\Models\Machinery;
use App\Models\User;
use Illuminate\Database\Seeder;

class MachinerySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        Machinery::factory(10)
            ->recycle($users)
            ->create();
    }
}
