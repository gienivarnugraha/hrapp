<?php

namespace Database\Seeders;

use App\Enums\PositionEnum;
use App\Models\Competency;
use App\Models\Event;
use App\Models\JobTitle;
use App\Models\People;
use Faker\Factory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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
        ->hasAttached(
          Competency::inRandomOrder()->limit(random_int(1, 6))->get(),
          ['grade' => random_int(1, 9) * 10]
        )
        ->create([
          "nik" => $data['0'],
          "name" => $data['1'],
          "org" => $data['2'],
          "job_title_id" => intval($data['3']),
          'position' =>  $faker->randomElement([PositionEnum::Junior, PositionEnum::Medior, PositionEnum::Senior]),
        ]);
    }

    fclose($csvFile);
  }
}
