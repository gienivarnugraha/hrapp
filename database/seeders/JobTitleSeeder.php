<?php

namespace Database\Seeders;

use App\Models\JobTitle;
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
  
        $firstline = true;
        while (($data = fgetcsv($csvFile)) !== FALSE) {
            if (!$firstline) {
                JobTitle::create([
                    "name" => $data['0'],
                ]);    
            }
            $firstline = false;
        }
   
        fclose($csvFile);
    }
}
