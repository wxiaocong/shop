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
class UpdateUsersTable20180904172118 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('business_audit_state')->default(0)->after('is_business')->comment('商家审核状态:0默认无,1申请,2通过3拒绝');
            $table->string('business_audit_remark', 200)->nullable()->after('business_audit_state')->comment('审核备注');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function ($table) {
            $table->dropColumn('business_audit_state');
            $table->dropColumn('business_audit_remark');
        });
    }
}
