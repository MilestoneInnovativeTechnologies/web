<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationAudiencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_audiences', function (Blueprint $table) {
            $table->increments('id');
            $table->char('notification',15)->index();
            $table->char('partner',15)->index()->nullable();
					
            $table->foreign('notification')->references('code')->on('notifications')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('notification_audiences');
    }
}
