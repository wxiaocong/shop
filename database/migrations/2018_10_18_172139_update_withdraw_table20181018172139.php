<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 *--------------------------------------------------------------------------
 * update withdraw table
 *--------------------------------------------------------------------------
 *
 *
 */
class UpdateWithdrawTable20181018172139 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('withdraw', function (Blueprint $table) {
            $table->tinyInteger('pay_type')->default(0)->after('amount')->comment('付款方式1.提现到银行卡，2到微信余额');
            $table->string('bank_code', 64)->default('')->after('pay_type')->comment('收款方开户行');
            $table->string('enc_bank_no', 64)->default('')->after('bank_code')->comment('收款方银行卡号');
            $table->tinyInteger('cmms_amt')->default(0)->after('amount')->comment('手续费金额');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('withdraw', function ($table) {
            $table->dropColumn('pay_type');
            $table->dropColumn('bank_code');
            $table->dropColumn('enc_bank_no');
            $table->dropColumn('cmms_amt');
        });
    }
}
