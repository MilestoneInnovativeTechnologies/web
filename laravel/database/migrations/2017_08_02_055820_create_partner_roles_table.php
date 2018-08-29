<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartnerRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner_roles', function (Blueprint $table) {
          $table->increments('id');
					$table->unsignedInteger("login");
					$table->char("partner",15)->nullable();
					$table->char("role",15);
					$table->enum("status",["ACTIVE","INACTIVE"])->default("ACTIVE");
					$table->char("created_by",15)->nullable();
          $table->timestamps();
					
					$table->unique(["login","role"]);
					
					$table->foreign("login")->on("partner_logins")->references("id")->onDelete("cascade")->onUpdate("cascade");
					$table->foreign("partner")->on("partners")->references("code")->onDelete("cascade")->onUpdate("cascade");
					$table->foreign("role")->on("roles")->references("code")->onDelete("cascade")->onUpdate("cascade");
					$table->foreign("created_by")->on("partners")->references("code")->onDelete("set null")->onUpdate("cascade");
        });
			
			DB::unprepared("
			CREATE TRIGGER `partner_roles_before_insert` BEFORE INSERT ON `partner_roles` FOR EACH ROW BEGIN
				DECLARE V_PARTNER CHAR(15);
				SELECT partner INTO V_PARTNER FROM partner_logins WHERE id = NEW.login;
				SET NEW.partner = V_PARTNER;
			END
			"); 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('partner_roles');
    }
}
