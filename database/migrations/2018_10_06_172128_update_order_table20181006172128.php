<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 *--------------------------------------------------------------------------
 * update order table
 *--------------------------------------------------------------------------
 *
 *
 */
class UpdateOrderTable20181006172128 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order', function (Blueprint $table) {
            $table->integer('province')->unsigned()->after('express_id')->comment('省');
            $table->integer('city')->unsigned()->after('province')->comment('市');
            $table->integer('area')->unsigned()->after('city')->comment('区');
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
            $table->dropColumn('province');
            $table->dropColumn('city');
            $table->dropColumn('area');
        });
    }
}
