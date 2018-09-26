<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 *--------------------------------------------------------------------------
 * create admin_users table
 *--------------------------------------------------------------------------
 *
 *
 */
class CreateAdminUsersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_users', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();

            $table->string('admin_name', 20)->comment('用户名');
            $table->string('password', 100)->comment('密码');
            $table->string('email', 30)->nullable()->comment('Email');
            $table->string('qq', 30)->nullable()->comment('QQ');
            $table->string('we_chat', 30)->nullable()->comment('微信');
            $table->string('phone', 20)->nullable()->comment('手机号');
            $table->string('last_ip', 30)->nullable()->comment('最后登录IP');
            $table->timestamp('last_time')->nullable()->comment('最后登录时间');

            // create index
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
        Schema::drop('admin_users');
    }
}
