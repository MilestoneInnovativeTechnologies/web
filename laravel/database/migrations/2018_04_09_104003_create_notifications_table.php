<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->char('code',15)->primary();
            $table->string('title',160)->index();
            $table->text('description_short')->nullable();
            $table->text('description')->nullable();
            $table->date('date');
            $table->date('notify_from');
            $table->date('notify_to');
            $table->enum('target',['public','distributor','dealer','customer','supportteam','supportagent'])->nullable();
            $table->enum('target_type',['All','Except','Only'])->default('All');
            $table->char('created_by',15)->index()->nullable();
            $table->enum('status',['ACTIVE','INACTIVE'])->default('Active');
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
        Schema::dropIfExists('notifications');
    }
}
