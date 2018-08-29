<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSupportAgentDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('support_agent_departments', function (Blueprint $table) {
            $table->increments('id');
						$table->char('agent',15)->index();
						$table->char('department',15)->nullable()->index();
						$table->char('assigned_by',15)->nullable()->index();
						$table->enum('status',['ACTIVE','INACTIVE']);
            $table->timestamps();

						$table->foreign('agent')->on('partners')->references('code')->onUpdate('cascade')->onDelete('cascade');
						$table->foreign('department')->on('support_departments')->references('code')->onUpdate('cascade')->onDelete('set null');
 						$table->foreign('assigned_by')->on('partners')->references('code')->onUpdate('cascade')->onDelete('cascade');
       });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('support_agent_departments');
    }
}
