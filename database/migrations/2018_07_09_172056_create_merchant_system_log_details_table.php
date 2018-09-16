<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 *--------------------------------------------------------------------------
 * create merchant_system_log_details table
 *--------------------------------------------------------------------------
 *
 * @author caopei(caopei@carnetmotor.com)
 *
 */
class CreateMerchantSystemLogDetailsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_system_log_details', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();

            $table->integer('system_log_id')->unsigned()->nullable()->comment('merchant_system_logs表ID');
            $table->string('field', 32);
            $table->string('field_comment', 64)->nullable();
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();

            // create index
            $table->index('deleted_at');
            $table->index('system_log_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('merchant_system_log_details');
    }
}
