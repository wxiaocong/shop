<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 *--------------------------------------------------------------------------
 * create attributes table
 *--------------------------------------------------------------------------
 *
 *
 */
class CreateAttributesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attributes', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();

            $table->integer('model_id')->comment('模型ID');
            $table->tinyInteger('type')->comment('1-单选框;2-复选框;3-下拉框;4-输入框');
            $table->string('name', 60)->comment('属性名称');
            $table->text('value')->nullable()->comment('数据');
            $table->tinyInteger('search')->comment('是否为商品筛选项：0-否;1-是');
            $table->tinyInteger('spec')->default(0)->comment('是否规格：0-否;1-是');

            $table->index('model_id');
            $table->index('search');
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
        Schema::dropIfExists('attributes');
    }
}
