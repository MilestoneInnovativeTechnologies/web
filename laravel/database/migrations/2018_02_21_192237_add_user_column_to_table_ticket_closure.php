<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserColumnToTableTicketClosure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ticket_closures', function (Blueprint $table) {
					$table->char('user',15)->nullable()->index()->after('reference_ticket');
					$table->foreign('user')->references('code')->on('partners')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ticket_closures', function (Blueprint $table) {
					$table->dropColumn('user');
        });
    }
}
