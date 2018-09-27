<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 *--------------------------------------------------------------------------
 * update system table
 *--------------------------------------------------------------------------
 *
 *
 */
class UpdateSystemTable20180927172125 extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('system', function (Blueprint $table) {
            $table->string('desc', 200)->nullable()->after('val')->comment('描述');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('system', function ($table) {
            $table->dropColumn('desc');
        });
    }
}
