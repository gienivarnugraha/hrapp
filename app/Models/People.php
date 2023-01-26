<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class People extends Model
{
    use HasFactory;

    protected $table = 'peoples';

    public function competencies() {
        return $this->belongsToMany(Competency::class);
    }

    public function jobTitle() {
        return $this->belongsTo(JobTitle::class);
    }
}
