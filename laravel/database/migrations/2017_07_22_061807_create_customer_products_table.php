<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_products', function (Blueprint $table) {
            $table->char('customer',15);
            $table->char('product',15);
            $table->char('edition',15);
            $table->unsignedTinyInteger('seqno')->default(1);
            $table->string('using_version',20)->nullable();
						$table->timestamp("lastused_on")->nullable();
            $table->string('downloaded_version',20)->nullable();
						$table->timestamp("downloaded_on")->nullable();
					
						$table->primary(['customer','product','edition','seqno']);
					
						$table->foreign('customer')->references("code")->on("partners")->onUpdate("cascade")->onDelete("restrict");
						$table->foreign('product')->references("code")->on("products")->onUpdate("cascade")->onDelete("restrict");
						$table->foreign('edition')->references("code")->on("editions")->onUpdate("cascade")->onDelete("restrict");
					
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_products');
    }
}
