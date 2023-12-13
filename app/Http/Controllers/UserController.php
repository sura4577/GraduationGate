<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::paginate();

        return view('users.index', compact('users'));
    }

    public function updateType(Request $request)
    {
        $user['user_type'] = $request->userType;
        User::where('id', $request->userId)->update($user);
        $users = User::paginate();
        return view('users.index', compact('users'));
    }
}
