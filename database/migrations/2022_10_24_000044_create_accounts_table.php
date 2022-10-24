<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Accounts Table.
 *
 * @version 0.1.0
 * @since 0.1.0
 * @category Tables
 * @author Caique Araujo <caique@piggly.com.br>
 * @license MIT
 * @copyright 2022 Caique Araujo <caique@piggly.com.br>
 */
class CreateAccountsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('accounts', function (Blueprint $table) {
			$table->bigInteger('local_id', true, true)->primary();
			$table->bigInteger('external_id', false, true)->comment('Organizze external ID')->index();
			$table->enum('type', ['checking', 'savings', 'other']);
			$table->string('name');
			$table->string('description');
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
		Schema::dropIfExists('accounts');
	}
}
