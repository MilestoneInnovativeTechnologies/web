<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerCookiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_cookies', function (Blueprint $table) {
            $table->increments('id');
            $table->char('customer',15)->index();
            $table->string('name',60)->nullable();
            $table->string('value',120)->nullable();
            $table->char('created_by',15)->index()->nullable();
            $table->timestamps();
						
						$table->foreign('customer')->references('code')->on('partners')->onUpdate('cascade')->onDelete('cascade');
						$table->foreign('created_by')->references('code')->on('partners')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_cookies');
    }
}
