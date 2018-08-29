<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartnerLoginsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner_logins', function (Blueprint $table) {
					$table->char('partner',15);
					$table->tinyInteger('seqno')->unsigned()->default(1);
					$table->string('email',100);
					$table->string('password');
					$table->rememberToken();
          $table->enum('status',['ACTIVE','INACTIVE'])->default('ACTIVE');
          $table->timestamps();
					
					$table->primary(['partner','seqno']);
					$table->foreign('partner')->references('code')->on('partners')->onUpdate('cascade')->onDelete('cascade');
					
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('partner_logins');
    }
}
