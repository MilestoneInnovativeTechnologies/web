<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePublicArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('public_articles', function (Blueprint $table) {
            $table->char('code',15)->primary();
            $table->string('name',190)->index()->nullable();
            $table->string('title',190)->nullable();
            $table->string('view',190)->nullable();
            $table->unsignedTinyInteger('count')->default(0);
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
        Schema::dropIfExists('public_articles');
    }
}
