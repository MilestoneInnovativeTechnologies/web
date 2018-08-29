<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyNameCaseSensitie extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('editions', function (Blueprint $table) {
            //
						DB::statement("ALTER TABLE `editions` MODIFY `name` VARCHAR(60) BINARY");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('editions', function (Blueprint $table) {
            //
						DB::statement("ALTER TABLE `editions` MODIFY `name` VARCHAR(60)");
        });
    }
}
