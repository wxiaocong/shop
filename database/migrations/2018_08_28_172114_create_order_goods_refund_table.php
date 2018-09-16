<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 *--------------------------------------------------------------------------
 * create order_goods_refund table
 *--------------------------------------------------------------------------
 *
 * @author caopei(caopei@carnetmotors.com)
 *
 */
class CreateOrderGoodsRefundTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_goods_refund', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();

            $table->integer('refund_id')->comment('订单退款ID');
            $table->integer('spec_id')->unsigned()->comment('skuID');
            $table->integer('refund_num')->default(0)->comment('退货数量');
            $table->integer('refund_fee')->default(0)->comment('退款金额');

            // create index
            $table->index('refund_id');
            $table->index('spec_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_goods_refund');
    }
}
