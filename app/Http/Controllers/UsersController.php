<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User; // 追加
use App\Micropost; // 追加（ユーザ情報の取得）

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
        //$microposts = $user->microposts()->orderBy('created_at', 'desc')->paginate(10);
        // 11.2：↑上記を以下に変更↓
        $microposts = $user->feed_microposts()->orderBy('created_at', 'desc')->paginate(10);
        $data = [
            'user' => $user,
            'microposts' => $microposts,
        ];

        $data += $this->counts($user);

        return view('users.show', $data);
    }
    
    // 10.4：フォロー情報の取得
    public function followings($id)
    {
        // idからユーザ調べる
        $user = User::find($id);
        
        // ユーザのフォロー数を調べる
        $followings = $user->followings()->paginate(10);

        $data = [
            'user' => $user,
            'users' => $followings,
        ];

        $data += $this->counts($user);

        return view('users.followings', $data);
    }

    public function followers($id)
    {
        $user = User::find($id);
        $followers = $user->followers()->paginate(10);

        $data = [
            'user' => $user,
            'users' => $followers,
        ];

        $data += $this->counts($user);

        return view('users.followers', $data);
    }
    
    /* 課題 */
    // お気に入りの一覧を表示するアクション
    public function favorites($id)
    {
        $user = User::find($id);
        $microposts = $user->feed_favorites()->orderBy('created_at', 'desc')->paginate(10); 
        
        $data = [
            'user' => $user,
            'microposts' => $microposts,
        ];

        $data += $this->counts($user);

        return view('users.favorites', $data);
    }
}