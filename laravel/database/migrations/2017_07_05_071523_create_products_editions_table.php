<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsEditionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products_editions', function (Blueprint $table) {
            $table->char('product',5)->index();
            $table->char('edition',5)->index();
						$table->integer('level')->default(1);
						$table->text('description')->nullable();
            $table->timestamps();
						$table->foreign('product')->references('code')->on('products')->onDelete('cascade')->onUpdate('cascade');
						$table->foreign('edition')->references('code')->on('editions')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products_editions');
    }
}
