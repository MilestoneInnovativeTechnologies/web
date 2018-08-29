<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePublicPrintObjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('public_print_objects', function (Blueprint $table) {
            $table->char('code',15)->primary();
            $table->string('name',100)->index();
            $table->string('description',1000)->nullable();
            $table->string('preview',1000)->nullable();
            $table->string('file',1000)->nullable();
            $table->unsignedMediumInteger('downloads')->default(0);
            $table->enum('status',['ACTIVE','INACTIVE'])->default('ACTIVE');
            $table->char('created_by',15)->index()->nullable();
            $table->timestamps();

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
        Schema::dropIfExists('public_print_objects');
    }
}
