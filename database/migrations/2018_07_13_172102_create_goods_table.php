<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 *--------------------------------------------------------------------------
 * create goods table
 *--------------------------------------------------------------------------
 *
 *
 */
class CreateGoodsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();

            $table->integer('category_id')->comment('分类ID');
            $table->integer('category_parent_id')->comment('分类父ID');
            $table->string('name', 200)->comment('商品名称');
            $table->bigInteger('sell_price')->comment('销售价格');
            $table->bigInteger('member_price')->comment('会员价格');
            $table->bigInteger('cost_price')->comment('成本价格');
            $table->integer('total_num')->default(0)->comment('库存');
            $table->integer('warning_num')->default(0)->comment('预警库存');
            $table->integer('sale_num')->default(0)->comment('销售数量');
            $table->string('img', 200)->nullable()->comment('商品主图');
            $table->tinyInteger('state')->default(0)->comment('商品状态 0正常 1已删除 2下架 ');
            $table->mediumText('content')->nullable()->comment('商品描述');
            $table->string('keywords', 255)->nullable()->comment('SEO关键词');
            $table->string('description', 255)->nullable()->comment('SEO描述');
            $table->integer('weight')->default(0)->comment('重量');
            $table->string('unit', 255)->nullable()->comment('计件单位。如:件,箱,个');
            $table->smallInteger('sort')->default(0)->comment('排序');
            $table->integer('goods_type')->unsigned()->default(0)->comment('模型ID');
            $table->tinyInteger('recommend')->default(0)->comment('推荐级别');
            $table->integer('click')->default(0)->comment('点击数');

            // create index
            $table->index('category_id');
            $table->index('category_parent_id');
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
