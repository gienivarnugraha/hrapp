<?php

namespace Database\Seeders;

use App\Models\Competency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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
                    "position" => $data['2'],
                ]);
            }
            $firstline = false;
        }

        fclose($csvFile);
    }
}
