<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPrimaryKeyToSameMissingTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
			DB::statement("ALTER TABLE product_edition_packages ADD PRIMARY KEY (`product`,`edition`,`package`)");
			DB::statement("ALTER TABLE product_edition_features ADD PRIMARY KEY (`product`,`edition`,`feature`)");
			DB::statement("ALTER TABLE products_features ADD PRIMARY KEY (`product`,`feature`)");
			DB::statement("ALTER TABLE products_editions ADD PRIMARY KEY (`product`,`edition`)");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
