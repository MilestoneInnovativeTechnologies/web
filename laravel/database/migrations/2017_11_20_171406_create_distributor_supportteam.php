<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDistributorSupportteam extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('distributor_supportteam', function (Blueprint $table) {
            $table->increments('id');
            $table->char('distributor',15)->index();
            $table->char('supportteam',15)->nullable()->index();
            $table->char('assigned_by',15)->nullable();
            $table->enum('status', ['ACTIVE','INACTIVE'])->default('ACTIVE');
            $table->timestamps();
						$table->foreign('distributor')->on('partners')->references('code')->onUpdate('cascade')->onDelete('cascade');
						$table->foreign('supportteam')->on('partners')->references('code')->onUpdate('cascade')->onDelete('set null');
						$table->foreign('assigned_by')->on('partners')->references('code')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('distributor_supportteam');
    }
}
