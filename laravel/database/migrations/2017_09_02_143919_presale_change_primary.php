<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PresaleChangePrimary extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('presale_customers', function (Blueprint $table) {
          DB::statement('ALTER TABLE `presale_customers` DROP PRIMARY KEY, ADD PRIMARY KEY (`partner`, `seqno`)');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('presale_customers', function (Blueprint $table) {
            //
        });
    }
}
