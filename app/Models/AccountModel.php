<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Account model.
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
class AccountModel extends Model
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
	protected $table = 'accounts';

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
		'archived' => 'boolean',
		'default' => 'boolean',
		'last_sync' => 'datetime'
	];
}
