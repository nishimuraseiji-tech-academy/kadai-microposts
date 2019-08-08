<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        //ログイン認証してるかどうかをif文で判断
        if (Auth::guard($guard)->check()) {

            //リダイレクト先を以下に変更：return redirect('/home');
            //ログインしてたら、/（トップページ）に自動的に飛ぶ
            return redirect('/');
        }

        return $next($request);
    }
}
