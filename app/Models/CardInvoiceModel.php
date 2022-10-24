<?php

namespace App;

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
class CardInvoiceModel extends Model
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
	protected $table = 'card_invoices';

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
		'date' => 'date',
		'starting_date' => 'date',
		'closing_date' => 'date',
		'amount_cents' => 'integer',
		'payment_amount_cents' => 'integer',
		'balance_cents' => 'integer',
		'previous_balance_cents' => 'integer',
		'last_sync' => 'datetime'
	];

	/**
	 * Card.
	 *
	 * @return void
	 * @since 0.1.0
	 */
	public function card()
	{
		return $this->belongsTo(CardModel::class, 'card_id', 'local_id');
	}
}
