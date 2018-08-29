<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePresaleCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('presale_customers', function (Blueprint $table) {
          $table->char('partner',15)->primary();
					$table->timestamp('startdate')->nullable();
					$table->timestamp('enddate')->nullable();
					$table->timestamp('extended_to')->nullable();
					$table->enum('status',['ACTIVE','INACTIVE'])->default('ACTIVE');
					$table->foreign("partner")->references("code")->on('partners')->onDelete('cascade')->onUpdate("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('presale_customers');
    }
}
