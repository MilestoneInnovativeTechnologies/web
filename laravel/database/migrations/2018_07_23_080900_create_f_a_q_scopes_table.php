<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFAQScopesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('faq_scopes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('question')->index();
            $table->enum('public',['YES','NO'])->default('NO');
            $table->enum('support',['YES','NO'])->default('NO');
            $table->enum('distributor',['YES','NO'])->default('NO');
            $table->enum('customer',['YES','NO'])->default('NO');
            $table->char('partner',15)->nullable();
            $table->timestamps();

            $table->foreign('question')->references('id')->on('faqs')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('partner')->references('code')->on('partners')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('faq_scopes');
    }
}
