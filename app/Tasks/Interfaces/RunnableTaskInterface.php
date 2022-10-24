<?php

namespace App\Tasks\Interfaces;

/**
 * Runnable task.
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
interface RunnableTaskInterface
{
	/**
	 * Run task.
	 *
	 * @since 0.1.0
	 * @return void
	 */
	public function run();
}
