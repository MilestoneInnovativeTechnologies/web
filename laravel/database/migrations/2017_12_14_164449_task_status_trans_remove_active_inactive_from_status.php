<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TaskStatusTransRemoveActiveInactiveFromStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('task_status_trans', function (Blueprint $table) {
					DB::statement("ALTER TABLE `task_status_trans` CHANGE COLUMN `status` `status` ENUM('CREATED','ASSIGNED','OPENED','RECHECK','REASSIGNED','WORKING','HOLD','CLOSED') NOT NULL DEFAULT 'CREATED' COLLATE 'utf8mb4_unicode_ci' AFTER `task`");
        });
			
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('task_status_trans', function (Blueprint $table) {
            //
        });
    }
}
