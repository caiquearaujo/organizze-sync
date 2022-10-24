<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cards Table.
 *
 * @version 0.1.0
 * @since 0.1.0
 * @category Tables
 * @author Caique Araujo <caique@piggly.com.br>
 * @license MIT
 * @copyright 2022 Caique Araujo <caique@piggly.com.br>
 */
class CreateCardsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cards', function (Blueprint $table) {
			$table->bigInteger('local_id', true, true)->primary();
			$table->bigInteger('external_id', false, true)->comment('Organizze external ID')->index();
			$table->enum('type', ['credit_card', 'debit_card']);
			$table->string('name');
			$table->string('description')->nullable();
			$table->string('card_network');
			$table->unsignedTinyInteger('closing_day');
			$table->unsignedTinyInteger('due_day');
			$table->unsignedBigInteger('limit_cents');
			$table->boolean('archived')->default(false);
			$table->boolean('default')->default(false);

			$table->timestamp('last_sync')->nullable()->index();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('cards');
	}
}
