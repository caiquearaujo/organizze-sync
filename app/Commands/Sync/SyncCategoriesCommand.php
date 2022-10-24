<?php

namespace App\Commands\Sync;

use App\Commands\AbstractBaseCommand;
use App\Tasks\SyncCategoriesTask;
use Exception;

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
class SyncCategoriesCommand extends AbstractBaseCommand
{
	/**
	 * The signature of the command.
	 *
	 * @var string
	 * @since 0.1.0
	 */
	protected $signature = 'sync:categories';

	/**
	 * The description of the command.
	 *
	 * @var string
	 * @since 0.1.0
	 */
	protected $description = 'Sincroniza todas as categorias do Organizze com o banco de dados';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 * @since 0.1.0
	 */
	public function handle()
	{
		$this->introduction('Sincronização de Dados');
		$this->describe('As categorias do sistema serão sincronizadas.');

		$this->task('Dados importados', function () {
			try {
				(new SyncCategoriesTask())->run();
				return true;
			} catch (Exception $e) {
				return false;
			}
		});
	}
}
