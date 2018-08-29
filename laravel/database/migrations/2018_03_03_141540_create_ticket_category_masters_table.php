<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketCategoryMastersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_category_masters', function (Blueprint $table) {
            $table->char('code',15)->primary();
            $table->string('name',60)->index();
            $table->string('description',500)->nullable();
            $table->enum('priority',['NORMAL','LOW','HIGH','VERY LOW','VERY HIGH'])->default('NORMAL');
            $table->string('available',60)->index()->default('ALWAYS');
						$table->enum('status',['ACTIVE','INACTIVE'])->default('ACTIVE');
            $table->char('created_by',15)->index()->nullable();
            $table->string('specifications',400)->nullable();
            $table->timestamps();
						
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
        Schema::dropIfExists('ticket_category_masters');
    }
}
