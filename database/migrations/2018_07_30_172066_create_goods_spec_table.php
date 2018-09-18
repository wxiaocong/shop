<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 *--------------------------------------------------------------------------
 * create goods_spec table
 *--------------------------------------------------------------------------
 *
 * @author caopei(caopei@carnetmotors.com)
 *
 */
class CreateGoodsSpecTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_spec', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();

            $table->integer('goods_id')->unsigned()->comment('商品ID');
            $table->tinyInteger('type')->default(0);
            $table->string('attr_ids', 255);
            $table->string('values', 255);
            $table->integer('category_id')->comment('分类ID');
            $table->integer('category_parent_id')->comment('分类父ID');
            $table->string('name', 200)->comment('sku名称');
            $table->integer('number')->default(0)->comment('库存');
            $table->integer('wait_number')->unsigned()->default(0)->comment('待发货数量');
            $table->integer('warning_num')->default(0)->comment('预警库存');
            $table->bigInteger('sell_price')->comment('销售价格');
            $table->bigInteger('member_price')->comment('会员价格');
            $table->bigInteger('cost_price')->comment('成本价格');
            $table->integer('weight')->default(0)->comment('重量');
            $table->integer('click')->default(0)->comment('点击数');
            $table->integer('sale_num')->default(0)->comment('销售数量');
            $table->string('img', 200)->nullable()->comment('主图');
            $table->text('imgs')->nullable()->comment('明细组图');
            $table->tinyInteger('state')->default(0)->comment('商品状态 0正常 2下架 ');
            

            // create index
            $table->index('goods_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('goods_spec');
    }
}
