<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAgentTicketPrivilagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agent_ticket_privilages', function (Blueprint $table) {
            $table->increments('id');
            $table->char('agent',15)->index();
            $table->string('privilages',255)->nullable();
            $table->timestamps();
						$table->foreign('agent')->references('code')->on('partners')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agent_ticket_privilages');
    }
}
