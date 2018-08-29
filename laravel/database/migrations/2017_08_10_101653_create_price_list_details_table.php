<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePriceListDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('price_list_details', function (Blueprint $table) {
						$table->increments('id');
            $table->char('pricelist',15);
						$table->char('product',15);
						$table->char('edition',15);
						$table->decimal('price',30,10)->default(0);
						$table->string('currency',4);
            $table->timestamps();
					
					$table->unique(['pricelist','product','edition']);
					
					$table->foreign('pricelist')->references('code')->on('price_lists')->onUpdate('cascade')->onDelete('cascade');
					$table->foreign('product')->references('code')->on('products')->onUpdate('cascade')->onDelete('cascade');
					$table->foreign('edition')->references('code')->on('editions')->onUpdate('cascade')->onDelete('cascade');
					
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('price_list_details');
    }
}
