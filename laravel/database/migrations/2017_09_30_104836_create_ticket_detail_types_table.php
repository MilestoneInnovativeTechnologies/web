<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketDetailTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_detail_types', function (Blueprint $table) {
            $table->char('code',15)->primary();
            $table->string('name',60);
            $table->text('description')->nullable();
            $table->enum('status',["ACTIVE","INACTIVE"])->default("ACTIVE");
            $table->char('created_by',15)->nullable();
            $table->timestamps();
					
						$table->foreign('created_by')->on('partners')->references('code')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ticket_detail_types');
    }
}
