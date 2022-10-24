<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
			$table->bigInteger('local_id', true, true)->primary();
			$table->bigInteger('external_id', false, true)->comment('Organizze external ID')->index();
			$table->string('name');
			$table->char('color', 6);
			$table->bigInteger('parent_id', false, true)->nullable()->index();
			$table->timestamp('last_sync')->nullable()->index();

			// created_at and updated_at
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
