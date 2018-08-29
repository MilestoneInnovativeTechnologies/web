<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartnerRelationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner_relations', function (Blueprint $table) {
            $table->char('partner',15)->primary();
            $table->char('parent',15)->nullable();
					
						$table->foreign("partner")->references("code")->on("partners")->onUpdate("cascade")->onDelete("cascade");
						$table->foreign("parent")->references("code")->on("partners")->onUpdate("cascade")->onDelete("set null");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('partner_relations');
    }
}
