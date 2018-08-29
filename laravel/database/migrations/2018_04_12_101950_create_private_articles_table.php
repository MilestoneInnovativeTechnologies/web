<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePrivateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('private_articles', function (Blueprint $table) {
            $table->char('code',15)->primary();
            $table->string('name',190)->index()->nullable();
            $table->string('title',190)->nullable();
            $table->string('view',190)->nullable();
            $table->enum('target',['partner','distributor','dealer','customer','supportteam','supportagent'])->nullable();
            $table->enum('target_type',['All','Except','Only'])->default('All');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('private_articles');
    }
}
