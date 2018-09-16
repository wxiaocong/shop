<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 *--------------------------------------------------------------------------
 * create order table
 *--------------------------------------------------------------------------
 *
 * @author wangcong(wangcong@carnetmotors.com)
 *
 */
class CreateOrderTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();

            $table->string('order_sn', 32)->comment('订单号');
            $table->string('transaction_id', 32)->comment('微信支付订单号');
            $table->integer('user_id')->comment('下单人');
            $table->string('openid', 50)->nullable()->comment('下单人openid');
            $table->tinyInteger('pay_type')->default(1)->comment('支付方式1微信2支付宝3余额');
            $table->integer('payment')->comment('订单金额');
            $table->integer('express_fee')->default(0)->comment('运费');
            $table->integer('real_pay')->default(0)->comment('实付金额');
            $table->dateTime('pay_time')->nullable()->comment('付款时间');
            $table->dateTime('deliver_time')->nullable()->comment('发货时间');
            $table->integer('express_id')->comment('收货地址id');
            $table->string('receiver_name', 60)->comment('收货人');
            $table->string('receiver_mobile', 20)->comment('收货人手机号');
            $table->string('receiver_area', 60)->comment('收货区域');
            $table->string('receiver_address', 60)->comment('收货地址');
            $table->string('receiver_zip', 10)->nullable()->comment('邮编');
            $table->tinyInteger('state')->default(1)->comment('1等待付款,2已付款准备发货,3等待收货,6取消，7删除,8完成');
            $table->tinyInteger('deliver_status')->default(0)->comment('发货状态：0未发货，1部分发货, 2已发货');
            $table->string('message', 200)->nullable()->comment('用户留言');
            $table->string('remark', 200)->nullable()->comment('备注');
            
            // create index
            $table->index('user_id');
            $table->index('state');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order');
    }
}
