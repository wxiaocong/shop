<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 *--------------------------------------------------------------------------
 * create agent table
 *--------------------------------------------------------------------------
 *
 */
class CreateAgentTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('agent', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();

            $table->string('order_sn', 32)->comment('订单号');
            $table->string('transaction_id', 32)->comment('微信支付订单号');
            $table->integer('user_id')->comment('申请人');
            $table->integer('referee_id')->default(0)->comment('推荐人id');
            $table->string('openid', 50)->nullable()->comment('申请人openid');
            $table->tinyInteger('level')->default(1)->comment('申请级别');
            $table->integer('payment')->comment('订单金额');
            $table->integer('goodsNum')->default(0)->comment('配货数量');
            $table->string('agent_name', 100)->comment('代理商姓名');
            $table->string('mobile', 20)->comment('代理商手机号');
            $table->string('front_identity_card',200)->nullable()->comment('身份证正面');
            $table->string('back_identity_card',200)->nullable()->comment('身份证反面');
            $table->integer('province')->unsigned()->comment('省');
            $table->integer('city')->unsigned()->comment('市');
            $table->integer('area')->unsigned()->comment('区');
            $table->string('address', 200)->comment('详情地址');
            $table->tinyInteger('pay_type')->default(1)->comment('支付方式1微信2支付宝3余额');
            $table->integer('real_pay')->default(0)->comment('实付金额');
            $table->dateTime('pay_time')->nullable()->comment('付款时间');
            $table->tinyInteger('state')->default(1)->comment('1申请2已付款待审核3通过4退款');
            $table->string('remark', 200)->nullable()->comment('备注');

            $table->index('user_id');
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('agent');
    }
}
