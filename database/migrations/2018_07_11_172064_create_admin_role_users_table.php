<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 *--------------------------------------------------------------------------
 * create admin_role_users table
 *--------------------------------------------------------------------------
 *
 * @author caopei(caopei@carnetmotor.com)
 *
 */
class CreateAdminRoleUsersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_role_users', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();

            $table->integer('user_id')->unsigned()->comment('用户ID');
            $table->integer('role_id')->unsigned()->comment('角色ID');

            // create index
            $table->index('deleted_at');
            $table->index('user_id');
            $table->index('role_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('admin_role_users');
    }
}
