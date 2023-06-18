<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Enums\PositionEnum;
use App\Models\Competency;
use App\Models\Event;
use App\Models\JobTitle;
use App\Models\People;
use App\Models\User;
use Database\Seeders\CompetencySeeder;
use Database\Seeders\JobTitleSeeder;
use Database\Seeders\PeopleSeeder;
use Database\Seeders\RoleSeeder;
use Faker\Factory;
use Illuminate\Container\Container;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{


    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        collect(['MGR', 'ADMIN', 'SME', 'HRBP'])->each(fn($role) => $this->setRole($role) );

        $faker = Factory::create();

        JobTitleSeeder::run();

        CompetencySeeder::run();

        PeopleSeeder::run();

        JobTitle::all()->each(function (JobTitle $jobTitle) {
            collect([1,2,3])->each(function (int $position) use ($jobTitle) {
                $competency = Competency::inRandomOrder()->limit(random_int(1, 6))->get();

                $jobTitle->competencies()->attach($competency, ['position' => $position]);
            });
        });
    }

    public function setRole($name){
        $user = User::factory()->create([
            'email' =>  Str::lower($name) . '@admin.com',
        ]);

        $role = Role::create(['name'=>Str::upper($name)]);

        $user->assignRole($role);
    }
}
