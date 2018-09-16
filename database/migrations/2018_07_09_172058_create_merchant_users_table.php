<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 *--------------------------------------------------------------------------
 * create merchant_users table
 *--------------------------------------------------------------------------
 *
 * @author caopei(caopei@carnetmotor.com)
 *
 */
class CreateMerchantUsersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_users', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();

            $table->string('name', 60)->comment('用户名');
            $table->string('password', 100)->comment('密码');
            $table->tinyInteger('is_del')->unsigned()->default('0')->comment('0:未删除,1:已删除');
            $table->tinyInteger('is_lock')->unsigned()->default('0')->comment('0:未锁定,1:已锁定');
            $table->string('true_name', 60)->comment('商家真实名称');
            $table->string('email', 60)->nullable()->comment('Email');
            $table->string('qq', 30)->nullable()->comment('QQ');
            $table->string('we_chat', 60)->nullable()->comment('微信');
            $table->string('mobile', 20)->comment('手机号');
            $table->string('phone', 20)->nullable()->comment('座机号');
            $table->string('paper_img', 255)->nullable()->comment('执照证件照片');
            $table->integer('cash')->default(0)->comment('保证金');
            $table->integer('area_id')->unsigned()->comment('区ID');
            $table->string('address', 255)->comment('地址');
            $table->text('account')->comment('收款账号信息');
            $table->string('home_url', 255)->nullable()->comment('企业URL网站');
            $table->integer('tax')->unsigned()->default('0')->comment('税率');
            $table->integer('grade')->unsigned()->default('0')->comment('评分总数');
            $table->integer('sale')->unsigned()->default('0')->comment('总销量');
            $table->integer('comments')->unsigned()->default('0')->comment('评论次数');
            $table->string('logo', 255)->nullable()->comment('LOGO图标');
            $table->string('last_ip', 30)->nullable()->comment('最后登录IP');
            $table->timestamp('last_time')->nullable()->comment('最后登录时间');

            // create index
            $table->index('deleted_at');
            $table->index('true_name');
            $table->index('is_del');
            $table->index('is_lock');
            $table->index('email');
            $table->index('qq');
            $table->index('we_chat');
            $table->index('phone');
            $table->index('mobile');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('merchant_users');
    }
}
