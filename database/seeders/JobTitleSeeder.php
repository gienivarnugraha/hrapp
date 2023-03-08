<?php

namespace Database\Seeders;

use App\Models\JobTitle;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class JobTitleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
     public static function run()
    {
        $csvFile = fopen(base_path("database/csv/job.csv"), "r");
  
        while (($data = fgetcsv($csvFile)) !== FALSE) {
                JobTitle::create([
                    "id" => $data['0'],
                    "name" => $data['1'],
                    "user_id" => User::all()->random()->id
                ]);    
        }
   
        fclose($csvFile);
    }
}
