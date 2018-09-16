<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 *--------------------------------------------------------------------------
 * update goods_spec table
 *--------------------------------------------------------------------------
 *
 * @author wangcong(wangcong@carnetmotor.com)
 *
 */
class UpdateGoodsSpecTable20180903172117 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('goods_spec', function (Blueprint $table) {
            $table->integer('wait_number')->unsigned()->default(0)->after('number')->comment('待发货数量');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('goods_spec', function ($table) {
            $table->dropColumn('wait_number');
        });
    }
}
