<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Models\JobTitle;
use App\Models\Competency;
use Illuminate\Database\Seeder;
use Database\Seeders\RoleSeeder;
use Spatie\Permission\Models\Role;
use Database\Seeders\JobTitleSeeder;
use Database\Seeders\CompetencySeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        JobTitleSeeder::run();
        
        CompetencySeeder::run();

        RoleSeeder::run();

        $user = User::factory()
            ->count(10)
            ->create()
            ->each(function (User $user) {
                $role = Role::all()->random();

                $competency = Competency::inRandomOrder()->limit(random_int(1,6))->get();

                $user->competencies()->attach($competency);

                $user->assignRole($role);
            });

    }
}
