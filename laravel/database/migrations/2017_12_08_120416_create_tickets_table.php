<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
          $table->char('code',15)->primary();
					$table->string('title',100)->index();
					$table->text('description')->nullable();
					$table->char('ticket_type',15)->index()->nullable();
					$table->char('category',15)->index()->nullable();
					$table->enum('priority',['NORMAL','HIGH','LOW','VERY HIGH','VERY LOW'])->default('LOW');
					$table->char('customer',15)->index()->nullable();
					$table->unsignedTinyInteger('seqno')->default(1);
					$table->char('product',15)->index()->nullable();
					$table->char('edition',15)->index()->nullable();
					$table->char('created_by',15)->index()->nullable();
          $table->timestamps();

					$table->foreign('ticket_type')->references('code')->on('ticket_types')->onUpdate('cascade')->onDelete('set null');
					$table->foreign('category')->references('code')->on('ticket_categories')->onUpdate('cascade')->onDelete('set null');
					$table->foreign('customer')->references('code')->on('partners')->onUpdate('cascade')->onDelete('set null');
					$table->foreign('product')->references('code')->on('products')->onUpdate('cascade')->onDelete('set null');
					$table->foreign('edition')->references('code')->on('editions')->onUpdate('cascade')->onDelete('set null');
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
        Schema::dropIfExists('tickets');
    }
}
