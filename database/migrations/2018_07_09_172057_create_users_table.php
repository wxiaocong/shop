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
class CreateUsersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     * (暂时不做)
     * 锁定总次数
     * 最近一次锁定操作人
     * 最近一次锁定时间
     * 解锁总次数
     * 最近一次解锁操作人
     * 最近一次解锁时间
     * 总销售额
     * QQ号
     * 微信号
     * 感兴趣的分类
     * 姓名（实名可选）
     * 身份证号码（实名可选）
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();

            $table->string('mobile', 20)->nullable()->comment('手机号');
            $table->string('password', 100)->comment('密码');
            $table->string('openid', 50)->nullable()->comment('微信openid');
            $table->string('headimgurl', 200)->nullable()->comment('微信头像');
            $table->string('nickname', 100)->nullable()->comment('昵称');
            $table->tinyInteger('sex')->unsigned()->default(0)->comment('性别1男2女0未知');
            $table->date('birthday')->nullable()->comment('生日');
            $table->integer('province')->unsigned()->nullable()->comment('省');
            $table->integer('city')->unsigned()->nullable()->comment('市');
            $table->integer('area')->unsigned()->nullable()->comment('区');
            $table->string('address', 200)->nullable()->comment('联系地址');
            $table->string('zip', 10)->nullable()->comment('邮政编码');
            $table->string('phone', 20)->nullable()->comment('手机号');
            $table->string('email', 60)->nullable()->comment('Email');
            $table->tinyInteger('state')->unsigned()->default(1)->comment('状态1正常2锁定');
            $table->integer('balance')->unsigned()->default(0)->comment('用户余额');

            // create index
            $table->index('mobile');
            $table->index('deleted_at');
            $table->index('state');
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
