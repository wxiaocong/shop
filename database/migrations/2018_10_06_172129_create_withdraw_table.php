<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 *--------------------------------------------------------------------------
 * create withdraw table
 *--------------------------------------------------------------------------
 *
 *
 */
class CreateWithdrawTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('withdraw', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();

            $table->string('order_sn', 32)->comment('订单号');
            $table->integer('user_id')->comment('提现人');
            $table->string('openid', 50)->nullable()->comment('提现人openid');
            $table->string('realname', 100)->nullable()->comment('真实姓名');
            $table->integer('amount')->comment('提现金额');
            $table->dateTime('pay_time')->nullable()->comment('付款时间');
            $table->string('desc', 200)->nullable()->comment('付款描述信息');
            $table->tinyInteger('state')->default(1)->comment('1等待付款,2已付款,3失败');
            
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
        Schema::dropIfExists('withdraw');
    }
}