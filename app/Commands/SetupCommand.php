<?php

namespace App\Commands;

use App\Api\OrganizzeApi;
use Exception;
use sixlive\DotenvEditor\DotenvEditor;

/**
 * Command for first setup app.
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
class SetupCommand extends AbstractBaseCommand
{
	/**
	 * The signature of the command.
	 *
	 * @var string
	 * @since 0.1.0
	 */
	protected $signature = 'setup';

	/**
	 * The description of the command.
	 *
	 * @var string
	 * @since 0.1.0
	 */
	protected $description = 'Executa os procedimentos de configuração e validação do sistema para operação da aplicação';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 * @since 0.1.0
	 */
	public function handle()
	{
		$this->introduction('Assistente de Configuração');
		$this->describe('Neste assistente você será guiado para uma configuração da aplicação. Siga as instruções.');

		$this->_checkRequirements();
		$this->call('config:integration');
		$this->call('config:mysql');
		$this->call('update');
		$this->call('sync:categories');
		$this->call('sync:accounts');
		$this->call('sync:cards');
		$this->call('sync:invoices');
	}

	/**
	 * Check if has requirements.
	 *
	 * @since 1.0.0
	 * @return boolean
	 */
	protected function _checkRequirements()
	{
		try {
			(new DotenvEditor())->load(config('storage.abspath').'/.env');
		} catch (Exception $e) {
			$this->attention(\sprintf('Verifique se o arquivo .env está disponível em: %s', config('storage.abspath')));
			$this->throwError($e->getMessage());
		}

		$requirements = true;

		$resp = $this->task('Extensão PHP MySQL', function () {
			return \extension_loaded('mysql') || \extension_loaded('mysqli');
		});

		$requirements = $requirements && $resp;

		if (!$requirements) {
			$this->attention('Certifique-se de ter instalado/ativado todos os requisitos');
			$this->throwError('Requisitos não satisfeitos');
		}
	}
}
