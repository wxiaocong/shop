<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 *--------------------------------------------------------------------------
 * create category table
 *--------------------------------------------------------------------------
 *
 *
 */
class CreateCategoryTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();

            $table->integer('parent_id')->default(0)->comment('上级id');
            $table->string('name', 50)->comment('分类名称');
            $table->string('pic', 100)->nullable()->comment('分类图片');
            $table->integer('sort')->default(99)->comment('排序');
            $table->tinyInteger('state')->default(1)->comment('1前台显示2前台不显示');

            // create index
            $table->index('parent_id', 'name');
            $table->index('parent_id');
            $table->index('sort');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('category');
    }
}
