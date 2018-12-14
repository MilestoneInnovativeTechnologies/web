<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBackupRegistrationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('backup_registration', function (Blueprint $table) {
            //
            $table->increments('customer_id');
            $table->string('package_id');
            $table->string('db_username');
            $table->string('init_token');
            $table->string('port');
            $table->string('server_name');
            $table->date('start_date');
            $table->date('end_date');
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
        Schema::table('BackupRegistration', function (Blueprint $table) {
            //
        });
    }
}
