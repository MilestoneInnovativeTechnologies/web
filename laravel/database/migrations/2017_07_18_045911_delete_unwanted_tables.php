<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteUnwantedTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("DROP TABLE if exists `email_address_types`");
        DB::statement("DROP TABLE if exists `email_address`");
        DB::statement("DROP TABLE if exists `contact_number_types`");
        DB::statement("DROP TABLE if exists `contact_numbers`");
        DB::statement("DROP TABLE if exists `address_types`");
        DB::statement("DROP TABLE if exists `address`");
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
