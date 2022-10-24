<?php

namespace App\Commands\Config;

use App\Api\OrganizzeApi;
use App\Commands\AbstractBaseCommand;
use Exception;
use sixlive\DotenvEditor\DotenvEditor;

/**
 * Command for integration configs.
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
class IntegrationConfigCommand extends AbstractBaseCommand
{
	/**
	 * The signature of the command.
	 *
	 * @var string
	 * @since 0.1.0
	 */
	protected $signature = 'config:integration';

	/**
	 * The description of the command.
	 *
	 * @var string
	 * @since 0.1.0
	 */
	protected $description = 'Configura a integração com a API do Organizze';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 * @since 0.1.0
	 */
	public function handle()
	{
		try {
			$env = (new DotenvEditor())->load(config('storage.abspath').'/.env');
		} catch (Exception $e) {
			$this->attention(\sprintf('Verifique se o arquivo .env está disponível em: %s', config('storage.abspath')));
			$this->throwError($e->getMessage());
		}

		$this->introduction('Configurações da Aplicação');

		$APP_TIMEZONE = $this->required('Fuso Horário', env('APP_TIMEZONE', 'America/Sao_Paulo'));

		$INTEGRATION_NAME = $this->required('Nome Completo', env('INTEGRATION_NAME', ''));
		$INTEGRATION_EMAIL = $this->required('E-mail do Organizze', env('INTEGRATION_EMAIL', ''));
		$INTEGRATION_KEY = $this->required('Chave da API do Organizze', true, true);

		$env->set('APP_TIMEZONE', $APP_TIMEZONE);
		$env->set('INTEGRATION_NAME', "'$INTEGRATION_NAME'");
		$env->set('INTEGRATION_EMAIL', $INTEGRATION_EMAIL);
		$env->set('INTEGRATION_KEY', "'$INTEGRATION_KEY'");

		$testing = $this->task('Testando conexão com a API', function () use ($INTEGRATION_NAME, $INTEGRATION_EMAIL, $INTEGRATION_KEY) {
			$api = new OrganizzeApi($INTEGRATION_NAME, $INTEGRATION_EMAIL, $INTEGRATION_KEY);

			try {
				$api->categories();
				return true;
			} catch (Exception $e) {
				return false;
			}
		});

		if (!$testing) {
			$this->attention('Certifique-se de ter preenchido as informações corretamente');
			$this->throwError('As credenciais estão inválidas');
		}

		$env->save();
	}
}
