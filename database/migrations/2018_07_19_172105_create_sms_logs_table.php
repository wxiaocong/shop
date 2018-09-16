<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 *--------------------------------------------------------------------------
 * create sms_logs table
 *--------------------------------------------------------------------------
 *
 * @author wangcong(wangcong@carnetmotors.com)
 *
 */
class CreateSmsLogsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();
            
            $table->string('ip', 40);
            $table->integer('user_id')->unsigned()->nullable();
            $table->string('mobile', 32);
            $table->string('content', 512)->comment('短信内容');
            $table->tinyInteger('type')->comment('类型');
            $table->tinyInteger('status')->comment('状态');
            
            // create index
            $table->index('ip');
            $table->index('user_id');
            $table->index('mobile');
            $table->index('type');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sms_logs');
    }
}
