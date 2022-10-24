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
class CreateTransactionsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('transactions', function (Blueprint $table) {
			$table->bigInteger('local_id', true, true)->primary();
			$table->bigInteger('external_id', false, true)->comment('Organizze external ID')->index();

			$table->enum('kind', ['account', 'card', 'payment', 'oposite']);
			$table->enum('type', ['expense', 'revenue']);

			$table->bigInteger('category_id', false, true)->nullable();
			$table->json('tags')->nullable();

			$table->date('date');
			$table->string('description')->nullable();
			$table->text('notes')->nullable();

			$table->unsignedBigInteger('amount_cents');
			$table->unsignedTinyInteger('total_installments');
			$table->unsignedTinyInteger('installment');
			$table->unsignedTinyInteger('attachments_count');

			$table->bigInteger('account_id', false, true)->nullable();
			$table->bigInteger('card_id', false, true)->nullable();
			$table->bigInteger('card_invoice_id', false, true)->nullable();
			$table->bigInteger('paid_card_id', false, true)->nullable();
			$table->bigInteger('paid_card_invoice_id', false, true)->nullable();
			$table->bigInteger('oposite_transaction_id', false, true)->nullable();
			$table->bigInteger('oposite_account_id', false, true)->nullable();

			$table->boolean('paid');
			$table->boolean('recurring');

			$table->timestamp('last_sync')->nullable()->index();
			$table->timestamps();

			$table->foreign('category_id')->references('local_id')->on('categories')->restrictOnUpdate()->restrictOnDelete();
		});
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
