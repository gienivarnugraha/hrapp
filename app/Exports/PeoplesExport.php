<?php

namespace App\Exports;

use App\Enums\PositionEnum;
use App\Models\JobTitle;
use App\Models\People;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PeoplesExport implements FromArray, WithHeadings, WithStyles
{
  private $jobTitle;

  public function __construct($id)
  {
    $this->jobTitle = JobTitle::find($id);
  }


  public function array(): array
  {
    $competencies = $this->jobTitle->competencies;

    $count = count($this->headings());

    $positions = [
      'Junior' => array_fill(0, $count, ''),
      'Medior' => array_fill(0, $count, ''),
      'Senior' => array_fill(0, $count, ''),
    ];

    foreach ($competencies as $competency) {
      $index = array_search($competency->name, $this->headings(), true);
      if ($index !== false) {
        $positions[PositionEnum::from($competency->pivot->position)->name][$index] = 'V';
      }
    }

    $positions2 = Arr::map($positions, function ($position, $key) {
      return Arr::prepend($position, $key);
    });

    $peoples = People::with('competencies')->where('job_title_id', $this->jobTitle->id)->get();

    $skills = [];

    foreach ($peoples as $people) {
      $skills["{$people->name}-{$people->position->name}"] = array_fill(0, $count, '');

      foreach ($people->competencies->pluck('name')->all() as $key => $value) {
        $index = array_search($value, $this->headings(), true);

        if ($index !== false) {
          $skills["{$people->name}-{$people->position->name}"][$index] = 'V';
        }
      }
    }



    $peoples2 = Arr::map($skills, function ($skill, $key) {
      $skill[0] = $key;
      return $skill;
    });

    $merge = array_merge($positions2, $peoples2);

    return $merge;
  }

  public function headings(): array
  {
    $competencies = $this->jobTitle->competencies->pluck('name')->all();

    $competencies2 = Arr::prepend($competencies, 'Name');

    return $competencies2;
  }

  public function styles(Worksheet $sheet)
  {
    $count = count($this->headings());

    $lastCol = $this->numToAlpha($count);

    $sheet->getStyle("A1:{$lastCol}1")->getFont()->setBold(true);
    $sheet->getStyle("A1:{$lastCol}1")->getAlignment()->setTextRotation(90);
    $sheet->getStyle("A1:{$lastCol}1")->getFill()
      ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
      ->getStartColor()->setARGB('ffc5e1a5');

    $sheet->getStyle("A1:{$lastCol}1")->getBorders()->getAllBorders();
  }

  public function numToAlpha($num)
  {
    $r = '';
    for ($i = 1; $num >= 0 && $i < 10; $i++) {
      $r = chr(0x41 + ($num % pow(26, $i) / pow(26, $i - 1))) . $r;
      $num -= pow(26, $i);
    }
    return $r;
  }
}
