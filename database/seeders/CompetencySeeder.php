<?php

namespace Database\Seeders;

use App\Models\Competency;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CompetencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public static function run()
    {
        $csvFile = fopen(base_path("database/csv/competencies.csv"), "r");
  
        $firstline = true;
        while (($data = fgetcsv($csvFile)) !== FALSE) {
            if (!$firstline) {
                Competency::create([
                    "name" => $data['0'],
                    "type" => $data['1'],
                ]);    
            }
            $firstline = false;
        }
   
        fclose($csvFile);
    }
}
