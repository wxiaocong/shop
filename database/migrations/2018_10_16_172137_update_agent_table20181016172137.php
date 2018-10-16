<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 *--------------------------------------------------------------------------
 * update agent table
 *--------------------------------------------------------------------------
 *
 *
 */
class UpdateAgentTable20181016172137 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('agent', function (Blueprint $table) {
            $table->string('idCard',50)->nullable()->after('mobile')->comment('身份证号');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('agent', function ($table) {
            $table->dropColumn('idCard');
        });
    }
}
