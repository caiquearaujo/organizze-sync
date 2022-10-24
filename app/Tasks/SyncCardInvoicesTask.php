<?php

namespace App\Tasks;

use App\Api\OrganizzeApi;
use App\Models\CardInvoiceModel;
use App\Models\CardModel;
use App\Tasks\Interfaces\RunnableTaskInterface;
use App\Tasks\Interfaces\SynchronizableTaskInterface;
use Carbon\Carbon;
use DateTimeImmutable;

/**
 * Sync card invoices with database.
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
class SyncCardInvoicesTask implements RunnableTaskInterface, SynchronizableTaskInterface
{
	/**
	 * Current card model.
	 *
	 * @var CardModel
	 * @since 0.1.0
	 */
	protected CardModel $_current;

	/**
	 * Start date.
	 *
	 * @var DateTimeImmutable|null
	 * @since 0.1.0
	 */
	protected $_start_at;

	/**
	 * End date.
	 *
	 * @var DateTimeImmutable|null
	 * @since 0.1.0
	 */
	protected $_end_at;

	/**
	 * Construct with filters.
	 *
	 * @param DateTimeImmutable|null $start_at
	 * @param DateTimeImmutable|null $end_at
	 * @since 0.1.0
	 */
	public function __construct(DateTimeImmutable $start_at = null, DateTimeImmutable $end_at = null)
	{
		$this->_start_at = $start_at;
		$this->_end_at = $end_at;
	}

	/**
	 * Run task.
	 *
	 * @since 0.1.0
	 * @return void
	 */
	public function run()
	{
		$api = new OrganizzeApi(env('INTEGRATION_NAME'), env('INTEGRATION_EMAIL'), env('INTEGRATION_KEY'));

		foreach (CardModel::all() as $crd) {
			$this->_current = $crd;
			$invoices = $api->invoices($crd, $this->_start_at, $this->_end_at);

			if (empty($invoices)) {
				return;
			}

			foreach ($invoices as $_external) {
				$_local = CardInvoiceModel::where('external_id', $_external['id'])->where('card_id', $this->_current->local_id)->first();

				if (empty($_local)) {
					$this->create($_external);
					continue;
				}

				$this->sync($_external, $_local);
			}
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
		$inv = new CardInvoiceModel();

		$inv->external_id = $external['id'];
		$this->fill($inv, $external)->save();
	}

	/**
	 * Sync external data with local record.
	 *
	 * @param array $external
	 * @param CardInvoiceModel $local
	 * @since 0.1.0
	 * @return void
	 */
	public function sync(array $external, $local)
	{
		$this->fill($local, $external)->save();
	}

	/**
	 * Fill local with partial external date.
	 *
	 * @param mixed $local
	 * @param array $external
	 * @since 0.1.0
	 * @return CardInvoiceModel
	 */
	public function fill($local, array $external)
	{
		if (!empty($this->_current)) {
			$local->card()->associate($this->_current);
		}

		$local->date = new Carbon($external['date']);
		$local->starting_date = new Carbon($external['starting_date']);
		$local->closing_date = new Carbon($external['closing_date']);
		$local->amount_cents = $external['amount_cents'];
		$local->payment_amount_cents = $external['payment_amount_cents'];
		$local->balance_cents = $external['balance_cents'];
		$local->previous_balance_cents = $external['previous_balance_cents'];
		$local->last_sync = Carbon::now();

		return $local;
	}
}
