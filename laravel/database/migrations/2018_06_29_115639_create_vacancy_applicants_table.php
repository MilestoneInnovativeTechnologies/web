<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVacancyApplicantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vacancy_applicants', function (Blueprint $table) {
            $table->increments('id');
            $table->char('vacancy',15)->index();
            $table->string('name',100)->index()->nullable();
            $table->string('phone',100)->index()->nullable();
            $table->string('email',100)->index()->nullable();
            $table->string('message',400)->nullable();
            $table->string('resume',1000)->nullable();
            $table->timestamps();

            $table->foreign('vacancy')->references('code')->on('vacancies')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vacancy_applicants');
    }
}
