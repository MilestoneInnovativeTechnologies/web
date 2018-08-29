<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketCategorySpecificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_category_specifications', function (Blueprint $table) {
            $table->char('code',15)->primary();
            $table->string('name',60)->index();
            $table->string('description',500)->nullable();
            $table->enum('type',['SPEC','VALUE'])->default('VALUE');
            $table->char('spec',15)->index()->nullable();
            $table->char('created_by',15)->index()->nullable();
            $table->enum('status',['ACTIVE','INACTIVE'])->default('ACTIVE');
            $table->timestamps();
						
						//$table->foreign('spec')->references('code')->on('ticket_category_specifications')->onUpdate('cascade')->onDelete('set null');
						$table->foreign('created_by')->references('code')->on('partners')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ticket_category_specifications');
    }
}
