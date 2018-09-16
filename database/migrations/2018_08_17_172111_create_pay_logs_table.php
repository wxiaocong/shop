<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 *--------------------------------------------------------------------------
 * create pay_logs table
 *--------------------------------------------------------------------------
 *
 * @author wangcong(wangcong@carnetmotors.com)
 *
 */
class CreatePayLogsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pay_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();

            $table->integer('user_id')->comment('用户id');
            $table->string('openid', 50)->default('')->comment('微信openid');
            $table->tinyInteger('pay_type')->default(1)->comment('1订单付款,2用户充值,3后台充值,4退款');
            $table->integer('gain')->default(0)->comment('收入(分)');
            $table->integer('expense')->default(0)->comment('支出');
            $table->integer('balance')->default(0)->comment('余额');
            $table->integer('opera_id')->default(0)->comment('操作用户id');
            $table->integer('order_id')->default(0)->comment('订单id');
            $table->text('remark')->nullable()->comment('备注');
            
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
        Schema::dropIfExists('pay_logs');
    }
}
