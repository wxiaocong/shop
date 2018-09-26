<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 *--------------------------------------------------------------------------
 * update wechat_notice table
 *--------------------------------------------------------------------------
 *
 *
 */
class UpdateWechatNoticeTable20180827172114 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wechat_notice', function (Blueprint $table) {
            $table->renameColumn('open_id', 'openid');
            $table->text('template_data')->nullable()->after('template_id')->comment('通知内容');
            $table->tinyInteger('is_received')->default(0)->after('is_send')->comment('0未收1已收');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wechat_notice', function ($table) {
            $table->renameColumn('openid', 'open_id');
            $table->dropColumn('template_data');
            $table->dropColumn('is_received');
        });
    }
}
