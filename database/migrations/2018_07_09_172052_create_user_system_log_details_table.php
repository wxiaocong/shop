<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 *--------------------------------------------------------------------------
 * create user_system_log_details table
 *--------------------------------------------------------------------------
 *
 * @author caopei(caopei@carnetmotor.com)
 *
 */
class CreateUserSystemLogDetailsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_system_log_details', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();

            $table->integer('system_log_id')->unsigned()->nullable()->comment('user_system_logs表ID');
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
        Schema::drop('user_system_log_details');
    }
}
