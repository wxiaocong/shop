<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 *--------------------------------------------------------------------------
 * create admin_system_logs table
 *--------------------------------------------------------------------------
 *
 * @author caopei(caopei@carnetmotor.com)
 *
 */
class CreateAdminSystemLogsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_system_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();

            $table->string('type', 1)->default('a')->comment('操作类型：a 添加;e 编辑；d 删除');
            $table->integer('table_id')->nullable()->comment('操作的数据表记录ID');
            $table->string('table_name', 60)->comment('操作的数据表名称');
            $table->string('table_remark', 60)->nullable()->comment('数据表说明');
            $table->integer('user_id')->nullable()->comment('操作人');

            // create index
            $table->index('deleted_at');
            $table->index('type');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('admin_system_logs');
    }
}
