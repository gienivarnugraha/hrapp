<?php

namespace Database\Seeders;

use Faker\Factory;
use App\Models\Event;
use App\Models\People;
use App\Models\JobTitle;
use App\Models\Competency;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PeopleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public static function run()
    {
        $faker = Factory::create();

        $csvFile = fopen(base_path("database/csv/people.csv"), "r");
  
        while (($data = fgetcsv($csvFile)) !== FALSE) {
                People::factory()
                // ->has(Event::factory()->count(2)->for(Competency::inRandomOrder()->first()))
                ->hasAttached(Competency::inRandomOrder()->limit(random_int(1, 6))->get())
                ->create([
                  "nik" => $data['0'],
                  "name" => $data['1'],
                  "org" => $data['2'],
                  "job_title_id" => intval($data['3']),
                  'position' =>  $faker->randomElement(['junior','medior','senior']),
                ]);
        }
   
        fclose($csvFile);
    }
}
