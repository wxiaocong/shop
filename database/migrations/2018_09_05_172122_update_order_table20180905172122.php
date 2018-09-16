<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 *--------------------------------------------------------------------------
 * update users table
 *--------------------------------------------------------------------------
 *
 * @author wangcong(wangcong@carnetmotor.com)
 *
 */
class UpdateOrderTable20180905172122 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order', function (Blueprint $table) {
            $table->string('out_trade_no', 32)->nullable()->after('order_sn')->comment('商户订单号');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order', function ($table) {
            $table->dropColumn('out_trade_no');
        });
    }
}
