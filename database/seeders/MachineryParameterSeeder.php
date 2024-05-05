<?php

namespace Database\Seeders;

use App\Models\Machinery;
use App\Models\MachineryParameter;
use App\Models\User;
use Illuminate\Database\Seeder;

class MachineryParameterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $machineries = Machinery::all();

        MachineryParameter::factory(15)
            ->recycle($users)
            ->recycle($machineries)
            ->create();
    }
}
