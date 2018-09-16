<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 *--------------------------------------------------------------------------
 * create brand table
 *--------------------------------------------------------------------------
 *
 * @author wangcong(wangcong@carnetmotors.com)
 *
 */
class CreateBrandTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brand', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();

            $table->string('logo_cname', 250)->comment('品牌中文名');
            $table->string('logo_ename', 250)->comment('品牌英文名');
            $table->string('short_name', 250)->comment('简称');
            $table->string('logo', 250)->nullable()->comment('logo图片');
            $table->string('mini_desc', 250)->nullable()->comment('一句话介绍');
            $table->text('short_desc')->nullable()->comment('文字简介');
            $table->text('detail_desc')->nullable()->comment('详细介绍');
            $table->tinyInteger('state')->default(1)->comment('品牌状态 0无效 1有效');
            $table->smallInteger('sort')->default(0)->comment('排序');

            // create index
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('brand');
    }
}
