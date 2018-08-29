<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoleResourceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('role_resource', function (Blueprint $table) {
            $table->char('role',15);
						$table->char('resource',15);
						$table->decimal('action',31,30)->unsigned()->default("0.1");
						$table->primary(['role','resource']);
						$table->foreign('role')->references('code')->on('roles')->onUpdate("cascade")->onDelete("cascade");
						$table->foreign('resource')->references('code')->on('resources')->onUpdate("cascade")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('role_resource');
    }
}
