<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBrandingProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branding_products', function (Blueprint $table) {
            $table->increments('id');
						$table->unsignedInteger('brand')->index();
            $table->char('product',15)->index()->nullable();
            $table->char('edition',15)->index()->nullable();
            $table->timestamps();
					
						$table->foreign('brand')->references('id')->on('brandings')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('branding_products');
    }
}
