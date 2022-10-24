<?php

namespace App\Tasks;

use App\Api\OrganizzeApi;
use App\Models\CardModel;
use App\Models\CategoryModel;
use DateTimeImmutable;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Sync categories with database.
 *
 * @package \App
 * @subpackage \App\Tasks
 * @version 0.1.0
 * @since 0.1.0
 * @category Tasks
 * @author Caique Araujo <caique@piggly.com.br>
 * @license MIT
 * @copyright 2022 Caique Araujo <caique@piggly.com.br>
 */
class SyncCategoriesTask implements RunnableTaskInterface, SynchronizableTaskInterface
{
	/**
	 * Run task.
	 *
	 * @since 0.1.0
	 * @return void
	 */
	public function run()
	{
		$api = new OrganizzeApi(env('INTEGRATION_NAME'), env('INTEGRATION_EMAIL'), env('INTEGRATION_KEY'));
		$categories = $api->categories();

		if (empty($categories)) {
			return;
		}

		foreach ($categories as $_external) {
			$_local = CategoryModel::where('external_id', $_external['id'])->first();

			if (empty($_local)) {
				$this->create($_external);
				continue;
			}

			$this->sync($_external, $_local);
		}
	}

	/**
	 * Create a new local record with external data.
	 *
	 * @param array $external
	 * @since 0.1.0
	 * @return void
	 */
	public function create(array $external)
	{
		$category = new CategoryModel();

		$category->external_id = $external['id'];
		$this->sync($external, $category);
	}

	/**
	 * Sync external data with local record.
	 *
	 * @param array $external
	 * @param CategoryModel $local
	 * @since 0.1.0
	 * @return void
	 */
	public function sync(array $external, $local)
	{
		$local->name = $external['name'];
		$local->color = $external['color'];

		$local->save();
	}
}
