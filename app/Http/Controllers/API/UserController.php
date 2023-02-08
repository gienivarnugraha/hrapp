<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class UserController extends Controller
{


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $jobTitle
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        return new UserResource(Auth::user());
    }

}
