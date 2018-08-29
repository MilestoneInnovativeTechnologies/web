<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductEditionFeaturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_edition_features', function (Blueprint $table) {
            $table->char('product',5)->index();
            $table->char('edition',5)->index();
            $table->integer('feature')->unsigned()->index();
            $table->string('value');
            $table->integer('order')->default(1);
            $table->timestamps();
						$table->foreign('product')->references('code')->on('products')->onDelete('cascade')->onUpdate('cascade');
						$table->foreign('edition')->references('code')->on('editions')->onDelete('cascade')->onUpdate('cascade');
						$table->foreign('feature')->references('id')->on('features')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_edition_features');
    }
}
