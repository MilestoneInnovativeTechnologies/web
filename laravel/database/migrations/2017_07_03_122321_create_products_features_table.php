<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsFeaturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products_features', function (Blueprint $table) {
            $table->string('product')->index();
            $table->integer('feature')->unsigned()->index();
						$table->string('value');
						$table->integer('order')->default(1);
            $table->timestamps();
						$table->foreign('feature')->references('id')->on('features')->onDelete('cascade')->onUpdate('cascade');
						$table->foreign('product')->references('code')->on('products')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products_features');
    }
}
