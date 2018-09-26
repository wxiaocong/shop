<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 *--------------------------------------------------------------------------
 * create order_refund table
 *--------------------------------------------------------------------------
 *
 *
 */
class CreateOrderRefundTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_refund', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();

            $table->integer('user_id')->default(0)->comment('用户id');
            $table->string('order_sn', 32)->nullable()->comment('订单号');
            $table->string('out_refund_no', 64)->comment('退款单号');
            $table->integer('total_fee')->default(0)->comment('订单金额');
            $table->integer('refund_fee')->default(0)->comment('申请退款金额');
            $table->integer('real_refund_fee')->default(0)->comment('确认退款金额');
            $table->string('refund_desc', 80)->comment('退款原因');
            $table->integer('opera_id')->default(0)->comment('审核用户id');
            $table->string('success_time', 20)->nullable()->comment('退款成功时间');
            $table->tinyInteger('state')->default(0)->comment('0申请1已退2拒绝3取消');

            // create index
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
        Schema::dropIfExists('order_refund');
    }
}
