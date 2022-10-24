<?php

namespace App\Commands\Config;

use App\Api\OrganizzeApi;
use App\Commands\AbstractBaseCommand;
use Exception;
use Illuminate\Support\Facades\DB;
use sixlive\DotenvEditor\DotenvEditor;

/**
 * Command for MySQL configs.
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
class MySQLConfigCommand extends AbstractBaseCommand
{
	/**
	 * The signature of the command.
	 *
	 * @var string
	 * @since 0.1.0
	 */
	protected $signature = 'config:mysql';

	/**
	 * The description of the command.
	 *
	 * @var string
	 * @since 0.1.0
	 */
	protected $description = 'Configura o acesso ao banco de dados';

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

		$this->introduction('Configurações do Banco de Dados');

		$DB_HOST= $this->required('Host de Conexão', env('DB_HOST', '127.0.0.1'));
		$DB_PORT= intval($this->required('Porta de Conexão', env('DB_PORT', 3306)));
		$DB_DATABASE = $this->required('Nome do Banco de Dados', env('DB_DATABASE', ''));
		$DB_USERNAME = $this->required('Usuário', env('DB_USERNAME', ''));
		$DB_PASSWORD = $this->required('Senha', true, true);
		$DB_CHARSET = $this->required('Charset', env('DB_CHARSET', 'utf8mb4'));
		$DB_COLLATION = $this->required('Collation', env('DB_COLLATION', 'utf8mb4_unicode_ci'));

		$env->set('DB_CONNECTION', 'mysql');
		$env->set('DB_HOST', $DB_HOST);
		$env->set('DB_PORT', $DB_PORT);
		$env->set('DB_DATABASE', $DB_DATABASE);
		$env->set('DB_USERNAME', "'$DB_USERNAME'");
		$env->set('DB_PASSWORD', "'$DB_PASSWORD'");
		$env->set('DB_CHARSET', $DB_CHARSET);
		$env->set('DB_COLLATION', $DB_COLLATION);

		$env->save();

		$this->call('config:clear');

		$testing = $this->task('Testando conexão com o banco de dados', function () {
			try {
				$db = DB::connection()->getDatabaseName();
				return !empty($db);
			} catch (Exception $e) {
				return false;
			}
		});

		if (!$testing) {
			$this->attention('Certifique-se de ter preenchido as informações corretamente');
			$this->throwError('Banco de dados não disponível');
		}
	}
}
