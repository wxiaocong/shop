<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 *--------------------------------------------------------------------------
 * create users table
 *--------------------------------------------------------------------------
 *
 *
 */
class CreateUsersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();

            $table->string('mobile', 20)->nullable()->comment('手机号');
            $table->string('openid', 50)->nullable()->comment('微信openid');
            $table->string('headimgurl', 200)->nullable()->comment('微信头像');
            $table->string('nickname', 100)->nullable()->comment('昵称');
            $table->tinyInteger('subscribe')->default(0)->comment('是否关注1是0否');
            $table->string('subscribe_time', 20)->nullable()->comment('关注时间');
            $table->string('realname', 100)->nullable()->comment('真实姓名');
            $table->tinyInteger('sex')->unsigned()->default(0)->comment('性别1男2女0未知');
            $table->date('birthday')->nullable()->comment('生日');
            $table->string('city', 20)->nullable()->comment('用户所在城市');
            $table->string('country', 20)->nullable()->comment('国家');
            $table->string('province', 20)->nullable()->comment('省份');
            $table->string('email', 60)->nullable()->comment('Email');
            $table->tinyInteger('state')->unsigned()->default(1)->comment('状态1正常2锁定');
            $table->integer('balance')->unsigned()->default(0)->comment('用户余额');
            $table->tinyInteger('level')->unsigned()->default(0)->comment('用户级别');
            $table->tinyInteger('vip')->unsigned()->default(0)->comment('是否vip');
            
            // create index
            $table->index('mobile');
            $table->index('openid');
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
    }
}
