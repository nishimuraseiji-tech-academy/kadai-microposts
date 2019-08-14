<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    // Micropostsモデルとの関係を以下追記
    public function microposts()
    {
        return $this->hasMany(Micropost::class);
    }
    
    // 多対多の関係を以下追記
    // ユーザがフォローしている関係をbelongsToManyで表現
    public function followings()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
    }

    // ユーザがフォローされている関係をbelongsToManyで表現
    public function followers()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }
    
    // フォロー／フォロワーの関係を以下追記
    // フォロー
    public function follow($userId)
    {
        // 既にフォローしているかの確認
        $exist = $this->is_following($userId);
        // 相手が自分自身ではないかの確認
        $its_me = $this->id == $userId;
    
        // exist：フォロー済み　もしくは $its_meがTrue（正しい）なら
        if ($exist || $its_me) {
            // 既にフォローしていれば何もしない
            return false;
        } else {
            // 未フォローであればフォローする
            $this->followings()->attach($userId);
            return true;
        }
    }
    
    // アンフォロー
    public function unfollow($userId)
    {
        // 既にフォローしているかの確認
        $exist = $this->is_following($userId);
        // 相手が自分自身ではないかの確認
        $its_me = $this->id == $userId;
    
        // すでにフォローしてて、なおかつ相手は自分自身でないなら
        if ($exist && !$its_me) {
            // 既にフォローしていればフォローを外す
            $this->followings()->detach($userId);
            return true;
        } else {
            // 未フォローであれば何もしない
            return false;
        }
    }
    
    // フォローしてるかどうかをfollow_id（フォローしたid）と自身のidで確認
    public function is_following($userId)
    {
        return $this->followings()->where('follow_id', $userId)->exists();
    }
    
    // 11.1：タイムライン用のMicroposts（呟き）を取得
    public function feed_microposts()
    {
        //１：まずfollowing関数でフォローしてるユーザ情報取り出し
        //２：pluckでカラム名取り出し
        //３：$follow_user_idsという配列として格納
        //４：follow_user_idsの配列に、ユーザ（自分）のidを追加
        //５：Micropostのモデルに情報を飛ばす
        $follow_user_ids = $this->followings()->pluck('users.id')->toArray();
        $follow_user_ids[] = $this->id;
        return Micropost::whereIn('user_id', $follow_user_ids);
    }
    
}
