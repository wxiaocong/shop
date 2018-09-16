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
class UpdateOrderRefundTable20180905172119 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_refund', function (Blueprint $table) {
            $table->string('refund_id', 32)->nullable()->after('out_refund_no')->comment('微信退款单号');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_refund', function ($table) {
            $table->dropColumn('refund_id');
        });
    }
}
