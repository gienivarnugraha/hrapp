<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Competency extends Model
{
    use HasFactory;

    protected $fillable = ['competency','type'];

    public function users(){
        return $this->belongsToMany(User::class);
    }
}
