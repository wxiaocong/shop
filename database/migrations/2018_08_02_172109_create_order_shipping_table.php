<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 *--------------------------------------------------------------------------
 * create order_shipping table
 *--------------------------------------------------------------------------
 *
 *
 */
class CreateOrderShippingTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_shipping', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();

            $table->integer('order_goods_id')->unsigned()->comment('订单商品id');
            $table->string('express_name', 50)->comment('快递名称');
            $table->string('express_no', 20)->comment('快递单号');
            $table->dateTime('express_time')->comment('发货时间');
            
            // create index
            $table->index('order_goods_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_shipping');
    }
}
