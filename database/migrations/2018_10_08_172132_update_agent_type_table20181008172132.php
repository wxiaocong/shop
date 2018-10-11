<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 *--------------------------------------------------------------------------
 * update agent_type table
 *--------------------------------------------------------------------------
 *
 *
 */
class UpdateAgentTypeTable20181008172132 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('agent_type', function (Blueprint $table) {
            $table->integer('salesNum')->unsigned()->default(0)->after('returnMoney')->comment('返还销售数量限制');
            $table->integer('timeLimit')->unsigned()->default(0)->after('salesNum')->comment('返还时间限制(天)');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('agent_type', function ($table) {
            $table->dropColumn('salesNum');
            $table->dropColumn('timeLimit');
        });
    }
}
