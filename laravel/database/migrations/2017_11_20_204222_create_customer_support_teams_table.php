<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerSupportTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_support_teams', function (Blueprint $table) {
            $table->increments('id');
            $table->char('customer',15)->index();
            $table->char('supportteam',15)->nullable()->index();
            $table->char('assigned_by',15)->nullable()->index();
            $table->char('product',15)->nullable()->index();
            $table->char('edition',15)->nullable()->index();
            $table->enum('status',['ACTIVE','INACTIVE'])->default('ACTIVE');
            $table->timestamps();
						$table->foreign('customer')->on('partners')->references('code')->onUpdate('cascade')->onDelete('cascade');
						$table->foreign('supportteam')->on('partners')->references('code')->onUpdate('cascade')->onDelete('set null');
						$table->foreign('assigned_by')->on('partners')->references('code')->onUpdate('cascade')->onDelete('set null');
						$table->foreign('product')->on('products')->references('code')->onUpdate('cascade')->onDelete('set null');
						$table->foreign('edition')->on('editions')->references('code')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_support_teams');
    }
}
