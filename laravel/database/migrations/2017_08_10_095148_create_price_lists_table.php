<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePriceListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('price_lists', function (Blueprint $table) {
            $table->char('code',15)->primary();
						$table->string('name',100);
						$table->text("description")->nullable();
						$table->char("created_by",15)->nullable();
						$table->enum("status",["ACTIVE","INACTIVE"])->default("ACTIVE");
            $table->timestamps();
					
						$table->foreign("created_by")->references("code")->on("partners")->onUpdate("cascade")->onDelete("set null");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('price_lists');
    }
}
