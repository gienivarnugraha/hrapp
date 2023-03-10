<?php

namespace App\Pivot;

use Illuminate\Database\Eloquent\Relations\Pivot;

class EventPeople extends Pivot
{

    protected $casts = [
        'attended' => 'boolean',
    ];
}
