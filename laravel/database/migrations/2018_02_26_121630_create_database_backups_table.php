<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDatabaseBackupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('database_backups', function (Blueprint $table) {
            $table->increments('id');
						$table->char('customer',15)->index();
						$table->string('details',120)->nullable();
						$table->string('file',500)->nullable();
						$table->string('mime',255)->nullable();
						$table->string('format',10)->nullable();
						$table->unsignedInteger('size')->default("0");
						$table->char('user',15)->index()->nullable();
						$table->enum('status',['WITHIN','OUTSIDE','INACTIVE'])->default('WITHIN');
            $table->timestamps();
						
						$table->foreign('customer')->references('code')->on('partners')->onUpdate('cascade')->onDelete('cascade');
						$table->foreign('user')->references('code')->on('partners')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('database_backups');
    }
}
