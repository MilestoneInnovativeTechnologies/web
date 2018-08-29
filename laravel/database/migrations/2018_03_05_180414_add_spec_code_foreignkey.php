<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSpecCodeForeignkey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ticket_category_specifications', function (Blueprint $table) {
					$table->foreign('spec')->references('code')->on('ticket_category_specifications')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ticket_category_specifications', function (Blueprint $table) {
					$table->dropForeign(['spec']);
            //
        });
    }
}
