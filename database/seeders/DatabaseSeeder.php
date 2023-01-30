<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Faker\Factory;
use App\Models\User;
use App\Models\Event;
use App\Models\People;
use App\Models\JobTitle;
use App\Models\Competency;
use Illuminate\Support\Arr;
use Illuminate\Database\Seeder;
use Database\Seeders\RoleSeeder;
use Spatie\Permission\Models\Role;
use Illuminate\Container\Container;
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

        $faker = Factory::create();

        JobTitleSeeder::run();

        CompetencySeeder::run();

        RoleSeeder::run();

        User::factory()
            ->count(3)
            ->create()
            ->each(function (User $user) {
                $role = Role::all()->random();
                $user->assignRole($role);
            });

        People::factory()
            ->count(10)
            ->has(Event::factory()->count(5))
            ->create()
            ->each(function (People $people) {

                $competency = Competency::inRandomOrder()->limit(random_int(1, 6))->get();

                $people->competencies()->attach($competency);
            });

        JobTitle::all()->each(function (JobTitle $jobTitle) {
            collect(['junior', 'medior', 'senior'])->each(function (string $position) use ($jobTitle) {
                $competency = Competency::inRandomOrder()->limit(random_int(1, 6))->get();

                $jobTitle->competencies()->attach($competency, ['position' => $position]);
            });
        });
    }
}
