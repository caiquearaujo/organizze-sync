<?php

namespace App\Tasks;

/**
 * Sync external data with database.
 *
 * @package \App
 * @subpackage \App\Tasks\Interfaces
 * @version 0.1.0
 * @since 0.1.0
 * @category Interfaces
 * @author Caique Araujo <caique@piggly.com.br>
 * @license MIT
 * @copyright 2022 Caique Araujo <caique@piggly.com.br>
 */
interface SynchronizableTaskInterface
{
	/**
	 * Run task.
	 *
	 * @since 0.1.0
	 * @return void
	 */
	public function run();

	/**
	 * Create a new local record with external data.
	 *
	 * @param array $external
	 * @since 0.1.0
	 * @return void
	 */
	public function create(array $external);

	/**
	 * Sync external data with local record.
	 *
	 * @param array $external
	 * @param mixed $local
	 * @since 0.1.0
	 * @return void
	 */
	public function sync(array $external, $local);
}
