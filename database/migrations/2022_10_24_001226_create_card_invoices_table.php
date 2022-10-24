<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Card invoices Table.
 *
 * @version 0.1.0
 * @since 0.1.0
 * @category Tables
 * @author Caique Araujo <caique@piggly.com.br>
 * @license MIT
 * @copyright 2022 Caique Araujo <caique@piggly.com.br>
 */
class CreateCardInvoicesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('card_invoices', function (Blueprint $table) {
			$table->bigInteger('local_id', true, true);
			$table->bigInteger('external_id', false, true)->comment('Organizze external ID')->index();
			$table->bigInteger('card_id', false, true)->nullable();
			$table->date('date');
			$table->date('starting_date');
			$table->date('closing_date');
			$table->unsignedBigInteger('amount_cents');
			$table->unsignedBigInteger('payment_amount_cents');
			$table->unsignedBigInteger('balance_cents');
			$table->unsignedBigInteger('previous_balance_cents');

			$table->timestamp('last_sync')->nullable()->index();
			$table->timestamps();

			$table->foreign('card_id')->references('local_id')->on('cards')->restrictOnUpdate()->restrictOnDelete();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('card_invoices');
	}
}
