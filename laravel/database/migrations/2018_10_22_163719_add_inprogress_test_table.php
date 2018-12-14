<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInprogressTestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('test_table', function (Blueprint $table) {
            //
            $table->string('type',10)->after('testname');
            $table->dropColumn('status');
           // $table->enum('status',['a','b','c'])->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('test_table', function (Blueprint $table) {
            //
        });
    }
}
