<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller {

    public function userIndex()
    {
        $test = User::where('name', 'teddy')->first();

        if (!$test) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($test);
    }

}