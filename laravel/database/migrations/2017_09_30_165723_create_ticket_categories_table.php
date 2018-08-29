<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_categories', function (Blueprint $table) {
            $table->char('code',15)->primary();
            $table->string('name',60)->index();
            $table->text('description')->nullable();
            $table->char('parent',15)->nullable();
            $table->unsignedTinyInteger('level')->default("0");
            $table->enum('priority',["NORMAL","HIGH","LOW","VERY HIGH","VERY LOW"])->default("NORMAL");
            $table->char('created_by',15)->nullable();
            $table->enum('status',["ACTIVE","INACTIVE"])->default("ACTIVE");
            $table->timestamps();
					
						$table->foreign('created_by')->on('partners')->references('code')->onUpdate('cascade')->onDelete('set null');
        });
				DB::statement('ALTER TABLE `ticket_categories` ADD CONSTRAINT `ticket_categories_parent_foreign` FOREIGN KEY (`parent`) REFERENCES `ticket_categories` (`code`) ON DELETE SET NULL ON UPDATE cascade');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ticket_categories');
    }
}
