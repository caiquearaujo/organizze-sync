<?php

namespace App\Tasks;

use App\Api\OrganizzeApi;
use App\Models\CardModel;
use App\Tasks\Interfaces\RunnableTaskInterface;
use App\Tasks\Interfaces\SynchronizableTaskInterface;
use Carbon\Carbon;

/**
 * Sync Cards with database.
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
class SyncCardsTask implements RunnableTaskInterface, SynchronizableTaskInterface
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
		$cards = $api->cards();

		if (empty($cards)) {
			return;
		}

		foreach ($cards as $_external) {
			$_local = CardModel::where('external_id', $_external['id'])->first();

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
		$crd = new CardModel();

		$crd->external_id = $external['id'];
		$this->fill($crd, $external)->save();
	}

	/**
	 * Sync external data with local record.
	 *
	 * @param array $external
	 * @param CardModel $local
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

		$this->fill($local, $external)->save();
	}

	/**
	 * Fill local with partial external date.
	 *
	 * @param mixed $local
	 * @param array $external
	 * @since 0.1.0
	 * @return CardModel
	 */
	public function fill($local, array $external)
	{
		$local->name = $external['name'];
		$local->description = $external['description'];
		$local->archived = $external['archived'];
		$local->default = $external['default'];
		$local->type = $external['type'];
		$local->card_network = $external['card_network'];
		$local->closing_day = $external['closing_day'];
		$local->due_day = $external['due_day'];
		$local->limit_cents = $external['limit_cents'];
		$local->last_sync = new Carbon($external['updated_at']);

		return $local;
	}
}
