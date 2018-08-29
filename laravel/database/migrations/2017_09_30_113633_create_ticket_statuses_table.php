<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_statuses', function (Blueprint $table) {
            $table->char('code',15)->primary();
            $table->string('name',60)->index();
            $table->text('description')->nullable();
            $table->string('customer_side_view',60)->nullable();
            $table->char('after',15)->nullable();
            $table->enum('agent_status',["NEW","INPROCESS","COMPLETED"])->default("NEW");
            $table->enum('customer_status',["NEW","INPROCESS","COMPLETED"])->default("NEW");
            $table->char('created_by',15)->nullable();
            $table->enum('status',["ACTIVE","INACTIVE"])->default("ACTIVE");
            $table->timestamps();
					
						$table->foreign('created_by')->on('partners')->references('code')->onUpdate('cascade')->onDelete('set null');
        });
			
				DB::statement('ALTER TABLE `ticket_statuses` ADD CONSTRAINT `ticket_statuses_after_foreign` FOREIGN KEY (`after`) REFERENCES `ticket_statuses` (`code`) ON DELETE SET NULL ON UPDATE cascade');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ticket_statuses');
    }
}
