<?php

namespace App\Exports\Sheets;

use App\Models\JobTitle;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;

class JobTitleSheet implements FromQuery, WithTitle
{
  private $name;

  public function __construct($name)
  {
    $this->name = $name;
  }

  /**
   * @return Builder
   */
  public function query()
  {
    $query = JobTitle::query()
      ->where('name', $this->name);


    return $query;
  }

  /**
   * @return string
   */
  public function title(): string
  {
    return $this->name;
  }
}
