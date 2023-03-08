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
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\PeopleSeeder;
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
        collect(['MGR', 'ADMIN', 'SME', 'HRBP'])->each(fn($role) => $this->setRole($role) );

        $faker = Factory::create();

        JobTitleSeeder::run();

        CompetencySeeder::run();
        
        PeopleSeeder::run();

        JobTitle::all()->each(function (JobTitle $jobTitle) {
            collect(['junior', 'medior', 'senior'])->each(function (string $position) use ($jobTitle) {
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
