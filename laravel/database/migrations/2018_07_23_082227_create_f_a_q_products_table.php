<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFAQProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('faq_products', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('question')->index();
            $table->char('product',15)->nullable();
            $table->char('edition',15)->nullable();
            $table->timestamps();

            $table->foreign('question')->references('id')->on('faqs')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('product')->references('code')->on('products')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('edition')->references('code')->on('editions')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('faq_products');
    }
}
