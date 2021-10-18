<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSkFeaturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sk_features', function (Blueprint $table) {
            $table->increments('id');
            $table->char('code',6)->index();
            $table->string('name',128)->index();
            $table->string('detail',4096)->nullable();
            $table->enum('type',['Yes/No','Detail'])->default('Yes/No');
            $table->string('default',256)->nullable();
            $table->integer('parent')->nullable()->default(null);
            $table->tinyInteger('level')->default(0);
            $table->enum('status',['Active','Inactive'])->default('Active');
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
        Schema::dropIfExists('sk_features');
    }
}
