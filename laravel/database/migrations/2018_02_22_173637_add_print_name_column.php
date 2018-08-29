<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPrintNameColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_print_objects', function (Blueprint $table) {
					$table->string('print_name',30)->nullable()->after('function_seq');
					$table->unsignedInteger('print_seq')->default(1)->after('print_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_print_objects', function (Blueprint $table) {
            //
        });
    }
}
