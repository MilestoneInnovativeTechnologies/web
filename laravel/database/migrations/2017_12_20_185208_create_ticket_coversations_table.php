<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketCoversationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_coversations', function (Blueprint $table) {
            $table->increments('id');
						$table->char('ticket',15)->index();
						$table->char('user',15)->index()->nullable();
						$table->enum('type',['CHAT','FILE','INFO'])->default('CHAT');
						$table->string('content',255)->nullable();
            $table->timestamps();
					
						$table->foreign('ticket')->references('code')->on('tickets')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('ticket_coversations');
    }
}
