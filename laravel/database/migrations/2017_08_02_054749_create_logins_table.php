<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoginsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner_logins', function (Blueprint $table) {
            $table->increments('id');
						$table->char("partner",15);
						$table->string("email",190);
						$table->string("password")->nullable();
						$table->string("remember_token")->nullable();
						$table->string("api_token")->nullable();
						$table->enum("status",["ACTIVE","INACTIVE"])->default("ACTIVE");
						$table->char("created_by",15)->nullable();
            $table->timestamps();
					
						$table->unique(["partner","email"]);
					
						$table->foreign("partner")->on("partners")->references("code")->onDelete("cascade")->onUpdate("cascade");
						$table->foreign("created_by")->on("partners")->references("code")->onDelete("cascade")->onUpdate("cascade");
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
