<?php

namespace Database\Seeders;

use App\Models\Machinery;
use App\Models\MachineryParameter;
use App\Models\Research;
use App\Models\User;
use Illuminate\Database\Seeder;

class ResearchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $machineries = Machinery::all();
        $machineryParameters = MachineryParameter::all();

        $researches = Research::factory(10)
            ->recycle($users)
            ->recycle($machineries)
            ->create();

        foreach ($researches as $research) {
            $research->parameters()->attach(
                $machineryParameters->filter(
                    function (MachineryParameter $machineryParameter) use ($research) {
                        return is_null($machineryParameter->machinery_id)
                            || $machineryParameter->machinery_id === $research->machinery_id;
                    })->pluck('id')->all()
            );

            if (! $research->is_public) {
                $research->participants()->attach(
                    $users->where('id', '!=', $research->author->id)->random(3)
                );
            }
        }
    }
}
