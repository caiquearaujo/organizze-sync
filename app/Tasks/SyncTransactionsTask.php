<?php

namespace App\Tasks;

use App\Api\OrganizzeApi;
use App\Models\AccountModel;
use App\Models\CardModel;
use App\Models\CategoryModel;
use App\Models\TransactionModel;
use App\Tasks\Interfaces\RunnableTaskInterface;
use App\Tasks\Interfaces\SynchronizableTaskInterface;
use Carbon\Carbon;
use DateTimeImmutable;

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
class SyncTransactionTask implements RunnableTaskInterface, SynchronizableTaskInterface
{
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
		$txs = $api->transactions($this->_start_at, $this->_end_at);

		if (empty($txs)) {
			return;
		}

		foreach ($txs as $_external) {
			$_local = TransactionModel::where('external_id', $_external['id'])->first();

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
		$acc = new TransactionModel();

		$acc->external_id = $external['id'];
		$this->fill($acc, $external)->save();
	}

	/**
	 * Sync external data with local record.
	 *
	 * @param array $external
	 * @param TransactionModel $local
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
	 * @param TransactionModel $local
	 * @param array $external
	 * @since 0.1.0
	 * @return TransactionModel
	 */
	public function fill($local, array $external)
	{
		$local->kind = !empty($external['credit_card_id']) ? 'card' : 'account';
		$local->operation = !empty($external['paid_credit_card_id']) ? 'payment' : (!empty($external['oposite_account_id']) ? 'oposite' : 'recular');
		$local->type = $external['amount_cents'] < 0 ? 'expense' : 'revenue';

		// Associate category
		$cat = CategoryModel::where('external_id', $external['category_id'])->first();

		if (!empty($cat)) {
			$local->category()->associate($cat);
		}

		// Associate account
		$acc = null;

		switch ($local->kind) {
			case 'card':
				$acc = CardModel::where('external_id', $external['account_id'])->first();
				break;
			case 'account':
				$acc = AccountModel::where('external_id', $external['account_id'])->first();
				break;
		}

		if (!empty($acc)) {
			$local->parent()->associate($acc);
		}

		$local->tags = $external['tags'];

		$local->date = new Carbon($external['date']);
		$local->description = $external['description'];
		$local->notes = $external['notes'];

		$local->amount_cents = $external['amount_cents'];
		$local->total_installments = $external['total_installments'];
		$local->installment = $external['installment'];
		$local->attachments_count = $external['attachments_count'];

		// External ID link...
		$local->card_id = $external['credit_card_id'];
		$local->card_invoice_id = $external['credit_card_invoice_id'];
		$local->paid_card_id = $external['paid_credit_card_id'];
		$local->paid_card_invoice_id = $external['paid_credit_card_invoice_id'];
		$local->oposite_transaction_id = $external['oposite_transaction_id'];
		$local->oposite_account_id = $external['oposite_account_id'];

		$local->paid = $external['paid'];
		$local->recurring = $external['recurring'];

		$local->last_sync = new Carbon($external['updated_at']);

		return $local;
	}
}
