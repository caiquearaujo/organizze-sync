<?php

namespace App\Commands;

/**
 * Command for update app.
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
class UpdateCommand extends AbstractBaseCommand
{
	/**
	 * The signature of the command.
	 *
	 * @var string
	 * @since 0.1.0
	 */
	protected $signature = 'update';

	/**
	 * The description of the command.
	 *
	 * @var string
	 * @since 0.1.0
	 */
	protected $description = 'Atualiza o sistema e o banco de dados';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 * @since 0.1.0
	 */
	public function handle()
	{
		$this->introduction('Atualização do Sistema');
		$this->describe('As operações de atualização do sistema serão executadas.');

		$this->call('migrate');
	}
}
