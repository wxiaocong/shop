<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 *--------------------------------------------------------------------------
 * create users table
 *--------------------------------------------------------------------------
 *
 * @author caopei(caopei@carnetmotor.com)
 *
 */
class CreateWechatUsersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wechat_users', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();

            $table->integer('user_id')->default(0)->comment('用户id');
            $table->string('nickname', 100)->nullable()->comment('昵称');
            $table->string('openid', 50)->nullable()->comment('微信openid');
            $table->tinyInteger('subscribe')->default(0)->comment('是否关注1是0否');
            $table->string('subscribe_time', 20)->nullable()->comment('关注时间');
            $table->string('headimgurl', 200)->nullable()->comment('微信头像');
            $table->string('city', 20)->nullable()->comment('用户所在城市');
            $table->string('country', 20)->nullable()->comment('国家');
            $table->string('province', 20)->nullable()->comment('省份');
            $table->tinyInteger('sex')->unsigned()->default(0)->comment('性别1男2女0未知');
            $table->timestamp('bind_time')->nullable()->comment('绑定时间');
            $table->timestamp('unbind_time')->nullable()->comment('解绑时间');
            $table->integer('total_visit')->default(0)->comment('访问总次数（根据session过期时间）');
            $table->string('last_ip', 30)->nullable()->comment('最后访问IP');
            $table->timestamp('last_time')->nullable()->comment('最近访问时间');

            // create index
            $table->index('user_id');
            $table->index('openid');
            $table->index('deleted_at');
        });

//         DB::statement("ALTER TABLE `wechat_users` comment'微信用户表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('wechat_users');
    }
}
