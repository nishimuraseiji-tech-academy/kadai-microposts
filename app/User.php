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
        //５：Micropostのモデルを使って、user_idとidが一致するツイートのみ抽出
        $follow_user_ids = $this->followings()->pluck('users.id')->toArray();
        $follow_user_ids[] = $this->id;
        return Micropost::whereIn('user_id', $follow_user_ids);
    }
    
    /*以下、課題*/

    /*多対多の関係（ユーザ側）*/
    public function favorites()
    {
        return $this->belongsToMany(Micropost::class, 'favorites', 'user_id', 'micropost_id')->withTimestamps();
    }
    
    public function favorite($micropostId)
    {
        // 既にお気に入りしているかの確認
        $exist = $this->is_favorite($micropostId);
        
        if ($exist) {
            // お気に入りしていれば何もしない
            return false;
            
        } else {
            // お気に入りしてなければ、お気に入りにする
            $this->favorites()->attach($micropostId);
            return true;
        }
    }

    public function unfavorite($micropostId)
    {
        // 既にお気に入りしているかの確認
        $exist = $this->is_favorite($micropostId);
        
        if ($exist) {
            // お気に入りしていれば、お気に入りツイートを外す
            $this->favorites()->detach($micropostId);
            return true;
            
        } else {
            // お気に入りしていないなら、お気に入りにする
            return false;
        }
    }

    public function is_favorite($micropostId)
    {
        return $this->favorites()->where('micropost_id', $micropostId)->exists();
    }
    
    public function feed_favorites()
    {
        // micropostsテーブルのうち、お気に入りしてるidのみデータ保存
        $favorite_microposts_ids = $this->favorites()->pluck('microposts.id')->toArray();
        
        // 自分のツイートのidも含む
        $favorite_microposts_ids[] = $this->id;
        
        // お気に入りしてるツイートをMicropostモデルを使ってreturnする
        return Micropost::whereIn('id', $favorite_microposts_ids);
    }
}
