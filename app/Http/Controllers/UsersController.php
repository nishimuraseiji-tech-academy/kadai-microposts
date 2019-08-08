<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User; // 追加


class UsersController extends Controller
{
    // indexページ用のコントローラ追加
    public function index()
    {
        $users = User::orderBy('id', 'desc')->paginate(10);

        return view('users.index', [
            'users' => $users,
        ]);
    }
    
    // showページ用のコントローラ追加
    public function show($id)
    {
        $user = User::find($id);

        return view('users.show', [
            'user' => $user,
        ]);
    }
    
}
