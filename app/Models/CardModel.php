<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Card model.
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
class CardModel extends Model
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
	protected $table = 'cards';

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
		'closing_day' => 'integer',
		'due_day' => 'integer',
		'limit_cents' => 'integer',
		'archived' => 'boolean',
		'default' => 'boolean',
		'last_sync' => 'datetime'
	];

	/**
	 * Invoices.
	 *
	 * @return void
	 * @since 0.1.0
	 */
	public function invoices()
	{
		return $this->hasMany(CardInvoiceModel::class, 'local_id', 'card_id');
	}
}
