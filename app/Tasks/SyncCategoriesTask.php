<?php

namespace App\Tasks;

use App\Api\OrganizzeApi;
use App\Models\CategoryModel;
use App\Tasks\Interfaces\RunnableTaskInterface;
use App\Tasks\Interfaces\SynchronizableTaskInterface;
use Carbon\Carbon;

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
		$cat = new CategoryModel();

		$cat->external_id = $external['id'];
		$this->fill($cat, $external)->save();
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
		$local->last_sync = Carbon::now();

		$this->fill($local, $external)->save();
	}

	/**
	 * Fill local with partial external date.
	 *
	 * @param mixed $local
	 * @param array $external
	 * @since 0.1.0
	 * @return CategoryModel
	 */
	public function fill($local, array $external)
	{
		$local->name = $external['name'];
		$local->color = $external['color'];
		$local->last_sync = Carbon::now();

		return $local;
	}
}
