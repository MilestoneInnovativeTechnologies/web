<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWebFieldToTablePublicPrintObjects extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('public_print_objects', function (Blueprint $table) {
            $table->enum('web',['Yes','No'])->default('Yes')->after('downloads');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('public_print_objects', function (Blueprint $table) {
            //
        });
    }
}
