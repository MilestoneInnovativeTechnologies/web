<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePDTablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pd_tables', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('pd')->index();
            $table->string('table',100)->nullable();
            $table->timestamp('last_created')->nullable();
            $table->timestamp('last_updated')->nullable();
            $table->enum('status',['Active','Inactive'])->default('Active');
            $table->timestamps();
            $table->foreign('pd')->references('id')->on('pd')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pd_tables');
    }
}
