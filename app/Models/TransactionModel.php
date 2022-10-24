<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Card invoice model.
 *
 * @package \App
 * @subpackage \App\Models
 * @version 0.1.0
 * @since 0.1.0
 * @category Models
 * @author Caique Araujo <caique@piggly.com.br>
 * @license MIT
 * @copyright 2022 Caique Araujo <caique@piggly.com.br>
 */
class TransactionModel extends Model
{
	/**
	 * Traits.
	 */
	use HasFactory;

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 * @since 0.1.0
	 */
	protected $table = 'transactions';

	/**
	 * The primary key associated with the table.
	 *
	 * @var string
	 * @since 0.1.0
	 */
	protected $primaryKey = 'local_id';

	/**
	 * The attributes that should be cast.
	 *
	 * @var array
	 * @since 0.1.0
	 */
	protected $casts = [
		'external_id' => 'integer',
		'tags' => 'array',
		'date' => 'date',
		'amount_cents' => 'integer',
		'total_installments' => 'integer',
		'installment' => 'integer',
		'attachments_count' => 'integer',
		'paid' => 'boolean',
		'recurring' => 'boolean',
		'last_sync' => 'datetime'
	];

	/**
	 * Parent.
	 *
	 * @return void
	 * @since 0.1.0
	 */
	public function parent()
	{
		switch ($this->kind) {
			case 'account':
				return $this->belongsTo(CardModel::class, 'account_id', 'local_id');
			case 'oposite':
				return $this->belongsTo(AccountModel::class, 'oposite_account_id', 'local_id');
			case 'card':
				return $this->belongsTo(CardModel::class, 'card_id', 'local_id');
			case 'payment':
				return $this->belongsTo(CardModel::class, 'paid_card_id', 'local_id');
		}

		return null;
	}
}
