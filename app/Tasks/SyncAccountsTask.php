<?php

namespace App\Tasks;

use App\Api\OrganizzeApi;
use App\Models\AccountModel;
use App\Models\CategoryModel;
use Carbon\Carbon;

/**
 * Sync accounts with database.
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
class SyncAccountsTask implements RunnableTaskInterface, SynchronizableTaskInterface
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
		$accounts = $api->accounts();

		if (empty($accounts)) {
			return;
		}

		foreach ($accounts as $_external) {
			$_local = AccountModel::where('external_id', $_external['id'])->first();

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
		$acc = new AccountModel();

		$acc->external_id = $external['id'];
		$acc->name = $external['name'];
		$acc->description = $external['description'];
		$acc->archived = $external['archived'];
		$acc->default = $external['default'];
		$acc->type = $external['type'];
		$acc->last_sync = new Carbon($external['updated_at']);

		$acc->save();
	}

	/**
	 * Sync external data with local record.
	 *
	 * @param array $external
	 * @param AccountModel $local
	 * @since 0.1.0
	 * @return void
	 */
	public function sync(array $external, $local)
	{
		$_updated_at = new Carbon($external['updated_at']);

		// Nothing to update
		if ($_updated_at->equalTo($local->last_sync)) {
			return;
		}

		$local->name = $external['name'];
		$local->description = $external['description'];
		$local->archived = $external['archived'];
		$local->default = $external['default'];
		$local->type = $external['type'];
		$local->last_sync = new Carbon($external['updated_at']);

		$local->save();
	}
}
