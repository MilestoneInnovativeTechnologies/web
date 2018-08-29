<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyProductNameCaseSensitie extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            //
						DB::statement("ALTER TABLE `products` MODIFY `name` VARCHAR(60) BINARY");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            //
						DB::statement("ALTER TABLE `products` MODIFY `name` VARCHAR(60)");
        });
    }
}
