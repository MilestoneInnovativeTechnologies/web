<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerPrintObjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_print_objects', function (Blueprint $table) {
					$table->char('code',15)->primary();
					$table->char('customer',15)->index()->nullable();
					$table->unsignedTinyInteger('reg_seq')->default(1);
					$table->unsignedTinyInteger('po_seq')->default(1);
					$table->char('function_code',15)->index();
					$table->string('function_name',30)->index()->nullable();
					$table->unsignedInteger('function_seq')->default(1);
					$table->string('file',400)->nullable();
					$table->string('preview',400)->nullable();
					$table->char('user',15)->nullable();
					$table->unsignedInteger('time')->default(0);
					$table->enum('status',['ACTIVE','INACTIVE'])->default('ACTIVE');
					$table->timestamps();

					$table->foreign('customer')->references('code')->on('partners')->onUpdate('cascade')->onDelete('set null');
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
        Schema::dropIfExists('customer_print_objects');
    }
}
