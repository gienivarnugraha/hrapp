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


    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request)
    {
        $request->user()->update($request->only('name'));

        return response()->json($request->user());
    }
}
