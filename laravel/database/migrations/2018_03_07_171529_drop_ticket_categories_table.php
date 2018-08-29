<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropTicketCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
				Schema::dropIfExists('ticket_categories');
//        Schema::table('ticket_categories', function (Blueprint $table) {
//            //
//					//$table->dropForeign(['parent']);
//        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ticket_categories', function (Blueprint $table) {
            //
        });
    }
}
