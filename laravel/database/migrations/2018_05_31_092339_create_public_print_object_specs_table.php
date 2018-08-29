<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePublicPrintObjectSpecsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('public_print_object_specs', function (Blueprint $table) {
            $table->increments('id');
            $table->char('print_object',15)->index();
            $table->string('spec0',40)->index()->nullable();
            $table->string('spec1',40)->index()->nullable();
            $table->string('spec2',40)->index()->nullable();
            $table->string('spec3',40)->index()->nullable();
            $table->string('spec4',40)->index()->nullable();
            $table->string('spec5',40)->index()->nullable();
            $table->string('spec6',40)->index()->nullable();
            $table->string('spec7',40)->index()->nullable();
            $table->string('spec8',40)->index()->nullable();
            $table->string('spec9',40)->index()->nullable();
            $table->timestamps();

            $table->foreign('print_object')->references('code')->on('public_print_objects')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('public_print_object_specs');
    }
}
