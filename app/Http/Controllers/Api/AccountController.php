<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserWriteRequest;

class AccountController extends Controller
{
    public function update(UserWriteRequest $request)
    {
        $user = auth()->user();

        $user->fill($request->all());

        $user->save();

        return response()->json($user);
    }
}
