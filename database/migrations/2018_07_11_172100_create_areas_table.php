<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 *--------------------------------------------------------------------------
 * create areas table
 *--------------------------------------------------------------------------
 *
 *
 */
class CreateAreasTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('areas', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();

            $table->integer('parent_id')->default(0)->comment('上级id');
            $table->string('area_name', 50)->comment('地区名称');
            $table->integer('sort')->default(99)->comment('排序');

            // create index
            $table->index('parent_id');
            $table->index('sort');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('areas');
    }
}
