<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaintenanceContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maintenance_contracts', function (Blueprint $table) {
					$table->char('code',15)->primary();
					$table->char('customer',15)->index()->nullable();
					$table->unsignedTinyInteger('registration_seq')->default(1);
					$table->unsignedTinyInteger('contract_seq')->default(1);
					$table->unsignedInteger('start_time')->default(0);
					$table->unsignedInteger('end_time')->default(0);
					$table->decimal('amount_actual',30,10)->default(0);
					$table->decimal('amount_paid',30,10)->default(0);
					$table->string('payment_note',255)->nullable();
					$table->char('renewed_to',15)->index()->nullable();
					$table->string('comments',255)->nullable();
					$table->timestamps();

					$table->foreign('customer')->references('code')->on('partners')->onUpdate('cascade')->onDelete('set null');
				});
			
			DB::statement('ALTER TABLE `maintenance_contracts` ADD CONSTRAINT `maintenance_contracts_renewed_to_foreign` FOREIGN KEY (`renewed_to`) REFERENCES `maintenance_contracts` (`code`) ON UPDATE CASCADE ON DELETE SET NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('maintenance_contracts');
    }
}
