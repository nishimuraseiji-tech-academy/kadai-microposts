<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Micropost extends Model
{
    //content,user_idというカラムしか引数を持ってこれないと解釈
    protected $fillable = ['content', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /*多対多の関係（microposts側）：使わないだろうけど一応記載しとく*/
    public function favorites_user()
    {
        return $this->belongsToMany(User::class, 'favorites', 'micropost_id', 'user_id')->withTimestamps();
    }

}