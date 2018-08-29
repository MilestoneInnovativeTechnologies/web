<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePrivateArticleAudiencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('private_article_audiences', function (Blueprint $table) {
            $table->increments('id');
            $table->char('article',15)->index();
            $table->char('partner',15)->index()->nullable();
					
            $table->foreign('article')->references('code')->on('private_articles')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('partner')->references('code')->on('partners')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('private_article_audiences');
    }
}
