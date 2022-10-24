<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Category Table.
 *
 * @version 0.1.0
 * @since 0.1.0
 * @category Tables
 * @author Caique Araujo <caique@piggly.com.br>
 * @license MIT
 * @copyright 2022 Caique Araujo <caique@piggly.com.br>
 */
class CreateCategoriesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('categories', function (Blueprint $table) {
			$table->bigInteger('local_id', true, true);
			$table->bigInteger('external_id', false, true)->comment('Organizze external ID')->index();
			$table->string('name');
			$table->char('color', 6)->nullable();
			$table->bigInteger('parent_id', false, true)->nullable()->index();

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
		Schema::dropIfExists('categories');
	}
}
