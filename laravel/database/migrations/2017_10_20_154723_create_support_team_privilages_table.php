<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSupportTeamPrivilagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('support_team_privilages', function (Blueprint $table) {
            $table->increments('id');
 						$table->char('support_team',15)->index();
						$table->enum('privilage',['NO','YES'])->default('NO');
            $table->timestamps();
					
						$table->foreign('support_team')->on('partners')->references('code')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('support_team_privilages');
    }
}
