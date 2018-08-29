<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->char('code',15)->primary();
						$table->char('distributor',15)->nullable()->index();
						$table->text('description')->nullable();
						$table->decimal('price',30,10)->default(0);
						$table->string('currency',4)->nullable();
						$table->decimal('exchange_rate',30,10)->default(1);
						$table->decimal('amount',30,10)->default(0);
						$table->enum('type',["+1","-1"])->default("-1");
						$table->enum('status',["PENDING","ACTIVE","INACTIVE"])->default("PENDING");
						$table->char('user',15)->nullable();
						$table->string('identifier',25)->nullable()->index();
            $table->timestamps();
					
						$table->foreign('distributor')->references('code')->on('partners')->onUpdate('cascade')->onDelete('set null');
						$table->foreign('user')->references('code')->on('partners')->onUpdate('cascade')->onDelete('set null');
        });
			
			DB::unprepared("CREATE TRIGGER `transactions_before_insert` BEFORE INSERT ON `transactions` FOR EACH ROW BEGIN SET NEW.amount = NEW.exchange_rate; END;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
