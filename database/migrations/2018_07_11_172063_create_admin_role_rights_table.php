<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 *--------------------------------------------------------------------------
 * create admin_role_rights table
 *--------------------------------------------------------------------------
 *
 *
 */
class CreateAdminRoleRightsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_role_rights', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();

            $table->integer('rights_id')->unsigned()->comment('权限ID');
            $table->integer('role_id')->unsigned()->comment('角色ID');

            // create index
            $table->index('deleted_at');
            $table->index('rights_id');
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
        Schema::drop('admin_role_rights');
    }
}
