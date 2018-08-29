<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRolenameColumnToPartnerRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('partner_roles', function (Blueprint $table) {
            $table->string("rolename",100)->nullable()->after("role");
        });
			
			DB::unprepared("DROP TRIGGER `partner_roles_before_insert`");

			DB::unprepared("
			CREATE TRIGGER `partner_roles_before_insert` BEFORE INSERT ON `partner_roles` FOR EACH ROW BEGIN
				DECLARE V_PARTNER CHAR(15);
				DECLARE ROLENAME VARCHAR(100);
				SELECT partner INTO V_PARTNER FROM partner_logins WHERE id = NEW.login;
				SELECT name INTO ROLENAME FROM roles WHERE code = NEW.role;
				SET NEW.partner = V_PARTNER;
				SET NEW.rolename = ROLENAME;
			END;
			"); 

			DB::unprepared("
			CREATE TRIGGER `partner_roles_before_update` BEFORE UPDATE ON `partner_roles` FOR EACH ROW BEGIN
				DECLARE V_PARTNER CHAR(15);
				DECLARE ROLENAME VARCHAR(100);
				SELECT partner INTO V_PARTNER FROM partner_logins WHERE id = NEW.login;
				SELECT name INTO ROLENAME FROM roles WHERE code = NEW.role;
				SET NEW.partner = V_PARTNER;
				SET NEW.rolename = ROLENAME;
			END;
			"); 

    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('partner_roles', function (Blueprint $table) {
            $table->dropColumn("rolename");
        });
			DB::unprepared("DROP TRIGGER `partner_roles_before_update`");
			DB::unprepared("DROP TRIGGER `partner_roles_before_insert`");
    }
}
