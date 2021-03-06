<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDistributorCustomerContactMethodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('distributor_customer_contact_methods', function (Blueprint $table) {
            $table->increments('id');
            $table->char('distributor',15)->index();
            $table->char('customer',15)->index()->nullable();
            $table->enum('email',['Yes','No'])->default('Yes');
            $table->char('sms',15)->index()->nullable();
            $table->char('assigned_by',15)->index()->nullable();
            $table->timestamps();
					
						$table->foreign('distributor')->references('code')->on('partners')->onUpdate('cascade')->onDelete('cascade');
						$table->foreign('customer')->references('code')->on('partners')->onUpdate('cascade')->onDelete('set null');
						$table->foreign('sms')->references('code')->on('sms_gateways')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('distributor_customer_contact_methods');
    }
}
