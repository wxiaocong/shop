<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 *--------------------------------------------------------------------------
 * create admin_rights table
 *--------------------------------------------------------------------------
 *
 * @author caopei(caopei@carnetmotor.com)
 *
 */
class CreateAdminRightsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_rights', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();

            $table->integer('user_id')->unsigned()->comment('创建人ID');
            $table->integer('category_id')->unsigned()->comment('权限分类ID');
            $table->string('name', 60)->comment('权限名称');
            $table->text('url')->comment('权限路由');
            $table->text('action')->comment('权限路径');
            $table->integer('sort_num')->unsigned()->default(1)->comment('排序编号');
            $table->tinyInteger('show_menu')->unsigned()->default('0')->comment('排序编号(0-隐藏,1-显示)');
            $table->string('description', 512)->nullable()->comment('描述');

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
        Schema::drop('admin_rights');
    }
}
