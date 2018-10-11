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
class UpdateAgentTable20181011172134 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('agent', function (Blueprint $table) {
            $table->string('transfer_voucher',200)->nullable()->after('pay_time')->comment('转账凭证');
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
            $table->dropColumn('transfer_voucher');
        });
    }
}
