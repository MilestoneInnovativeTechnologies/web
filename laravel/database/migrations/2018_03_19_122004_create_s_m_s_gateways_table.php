<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSMSGatewaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_gateways', function (Blueprint $table) {
            $table->char('code',15)->primary();
						$table->string('name',60)->index();
						$table->string('description',200)->nullable();
						$table->string('url',400)->nullable();
						$table->string('class',60)->nullable();
						$table->string('arg1',60)->nullable();
						$table->string('arg2',60)->nullable();
						$table->string('arg3',60)->nullable();
						$table->string('arg4',60)->nullable();
						$table->string('arg5',60)->nullable();
						$table->string('arg6',60)->nullable();
						$table->string('arg7',60)->nullable();
						$table->string('arg8',60)->nullable();
						$table->string('arg9',60)->nullable();
						$table->enum('status',['ACTIVE','INACTIVE'])->default('ACTIVE');
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
        Schema::dropIfExists('sms_gateways');
    }
}
