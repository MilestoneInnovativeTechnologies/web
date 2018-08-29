<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerRegistrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_registrations', function (Blueprint $table) {
          $table->char("customer",15);
          $table->char("product",15);
					$table->char("edition",15);
					$table->unsignedTinyInteger("seqno")->default(1);
					$table->string("lic_file")->nullable();
					$table->string("version",45)->nullable();
					$table->string("database",50)->nullable();
					$table->timestamp("date")->nullable();
					$table->string("serialno",60)->nullable();
					$table->string("key",128)->nullable();
					
					$table->primary(["customer","product","edition","seqno"]);
					
					$table->foreign("customer")->on("partners")->references("code")->onUpdate("cascade")->onDelete("restrict");
					$table->foreign("product")->on("products")->references("code")->onUpdate("cascade")->onDelete("restrict");
					$table->foreign("edition")->on("editions")->references("code")->onUpdate("cascade")->onDelete("restrict");
					
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_registrations');
    }
}
