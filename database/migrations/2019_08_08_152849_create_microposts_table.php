<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMicropostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('microposts', function (Blueprint $table) {
            $table->increments('id');
            //以下、２行追加(content:投稿内容)
            //indexをつけることで検索時間を速くする、unsignedで負のid番号を除去してる
            $table->integer('user_id')->unsigned()->index();
            $table->string('content');
            $table->timestamps();
            
            // 外部キー制約
            // データベース側の機能。
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('microposts');
    }
}
