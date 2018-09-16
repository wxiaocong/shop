<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 *--------------------------------------------------------------------------
 * create admin_rights_categories table
 *--------------------------------------------------------------------------
 *
 * @author caopei(caopei@carnetmotor.com)
 *
 */
class CreateAdminRightsCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_rights_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();

            $table->integer('user_id')->unsigned()->comment('创建人ID');
            $table->integer('parent_id')->unsigned()->nullable()->comment('父类ID');
            $table->string('name', 60)->comment('分类名称');
            $table->integer('sort_num')->unsigned()->default(1)->comment('排序编号');
            $table->tinyInteger('show_menu')->unsigned()->default('0')->comment('排序编号(0-隐藏,1-显示)');
            $table->string('description', 512)->nullable()->comment('描述');
            $table->string('menu_icon', 20)->nullable()->comment('菜单图标');

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
        Schema::drop('admin_rights_categories');
    }
}
