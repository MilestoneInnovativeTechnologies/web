<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewTicketCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_categories', function (Blueprint $table) {
            $table->increments('id');
						$table->char('ticket',15)->index()->nullable();
						$table->char('category',15)->index()->nullable();
						$table->char('specification',15)->index()->nullable();
						$table->char('value',15)->index()->nullable();
						$table->string('value_text',60)->nullable();
						$table->char('user',15)->index()->nullable();
            $table->timestamps();
						
						$table->foreign('ticket')->references('code')->on('tickets')->onUpdate('cascade')->onDelete('cascade');
						$table->foreign('category')->references('code')->on('ticket_category_masters')->onUpdate('cascade')->onDelete('set null');
						$table->foreign('specification')->references('code')->on('ticket_category_specifications')->onUpdate('cascade')->onDelete('set null');
						$table->foreign('value')->references('code')->on('ticket_category_specifications')->onUpdate('cascade')->onDelete('set null');
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
        Schema::dropIfExists('ticket_categories');
    }
}
