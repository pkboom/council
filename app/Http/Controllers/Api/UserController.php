<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function index()
    {
        return User::where('name', 'like', '%'.request('name').'%')
            ->take(5)
            ->pluck('name');
    }
}
