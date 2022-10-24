<?php

namespace App\Commands;

use Illuminate\Support\Facades\Log;
use LaravelZero\Framework\Commands\Command;

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
abstract class AbstractBaseCommand extends Command
{
	/**
	 * Ask for a required value.
	 *
	 * @param string $question
	 * @param mixed $default
	 * @return mixed
	 */
	public function required(string $question, $default = null, bool $secret = false)
	{
		do {
			$mtd = $secret ? 'secret' : 'ask';
			$value = $this->{$mtd}($question, $default);
			if (empty($value)) {
				$this->showError('O campo é obrigatório e deve ser preenchido');
			}
		} while (empty($value));

		return $value;
	}

	/**
	 * Print an attention.
	 *
	 * @param string $message
	 * @since 0.1.0
	 * @return void
	 */
	public function attention(string $message)
	{
		$this->line(\sprintf('<fg=yellow;options=bold>!! %s</>', $message));
		$this->newLine(1);
	}

	/**
	 * Print an introduction.
	 *
	 * @param string $message
	 * @since 0.1.0
	 * @return void
	 */
	public function introduction(string $message)
	{
		$this->line(\sprintf('<fg=yellow;options=bold,reverse>%s</>', $message));
		$this->newLine(1);
	}

	/**
	 * Describe a message by limiting line size
	 * by $wrap.
	 *
	 * @param string $message
	 * @param integer $wrap
	 * @since 0.1.0
	 * @return void
	 */
	public function describe(string $message, int $wrap = 10)
	{
		$this->line('===');

		$words   = \explode(' ', $message);
		$count   = 1;
		$message = '';

		foreach ($words as $word) {
			$message .= $word.' ';

			if ($count >= $wrap) {
				$this->line($message);
				$message = '';
				$count = 1;
				continue;
			}

			$count++;
		}

		if (!empty($message)) {
			$this->line($message);
		}

		$this->line('===');
		$this->newLine(1);
	}

	/**
	 * Print a success.
	 *
	 * @param string $message
	 * @since 0.1.0
	 * @return void
	 */
	public function success(string $message)
	{
		$this->newLine(1);
		$this->line(\sprintf('<fg=green;options=bold,reverse>%s</>', $message));
	}

	/**
	 * Print an error.
	 *
	 * @param string $message
	 * @since 0.1.0
	 * @return void
	 */
	public function showError(string $message)
	{
		$this->newLine(1);
		$this->line(\sprintf('<bg=red;options=bold>%s.</>', $message));
	}

	/**
	 * Print a success.
	 *
	 * @param string $message
	 * @since 0.1.0
	 * @return void
	 */
	public function exitSuccess(string $message)
	{
		$this->newLine(1);
		$this->line(\sprintf('<fg=green;options=bold>%s</>', $message));
		exit(0);
	}

	/**
	 * Exit with error message.
	 *
	 * @param string $message
	 * @since 0.1.0
	 * @return never
	 */
	public function throwError(string $message)
	{
		Log::error('CommandErrorException => '.$message);

		$this->newLine(1);
		$this->line(\sprintf('<bg=red;options=bold>Erro: %s.</>', $message));
		exit(1);
	}
}
