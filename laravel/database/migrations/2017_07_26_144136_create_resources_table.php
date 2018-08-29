<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resources', function (Blueprint $table) {
            $table->char('code',15)->primary();
            $table->string('name',120)->index();
            $table->string('displayname',120);
            $table->decimal('action',31,30)->unsigned()->default("0.1");
            $table->text('description')->nullable();
            $table->enum('status',['ACTIVE','INACTIVE'])->default("ACTIVE");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('resources');
    }
}
