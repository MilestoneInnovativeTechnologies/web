<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerRemoteConnectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_remote_connections', function (Blueprint $table) {
            $table->increments('id');
            $table->char('customer',15)->index();
            $table->string('appname',60)->nullable();
            $table->string('login',120)->nullable();
            $table->string('secret',120)->nullable();
            $table->text('remarks')->nullable();
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
        Schema::dropIfExists('customer_remote_connections');
    }
}
