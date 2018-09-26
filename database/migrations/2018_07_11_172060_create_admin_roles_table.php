<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 *--------------------------------------------------------------------------
 * create admin_roles table
 *--------------------------------------------------------------------------
 *
 *
 */
class CreateAdminRolesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_roles', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();

            $table->integer('user_id')->unsigned()->comment('创建人ID');
            $table->string('name', 60)->comment('角色名称');
            $table->string('description', 512)->nullable()->comment('描述');

            // create index
            $table->index('deleted_at');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('admin_roles');
    }
}
