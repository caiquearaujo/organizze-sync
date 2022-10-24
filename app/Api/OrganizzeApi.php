<?php

namespace App\Api;

use App\Models\CardModel;
use DateTimeImmutable;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * API integration wrapper.
 *
 * @package \App
 * @subpackage \App\Api
 * @version 0.1.0
 * @since 0.1.0
 * @category Api
 * @author Caique Araujo <caique@piggly.com.br>
 * @license MIT
 * @copyright 2022 Caique Araujo <caique@piggly.com.br>
 */
class OrganizzeApi
{
	/**
	 * Base URL.
	 *
	 * @var string
	 * @since 0.1.0
	 */
	protected string $_baseUrl = 'https://api.organizze.com.br/rest/v2';

	/**
	 * Integration e-mail.
	 *
	 * @var string
	 * @since 0.1.0
	 */
	protected string $_email;

	/**
	 * Integration key.
	 *
	 * @var string
	 * @since 0.1.0
	 */
	protected string $_key;

	/**
	 * Integration name.
	 *
	 * @var string
	 * @since 0.1.0
	 */
	protected string $_name;

	/**
	 * Construct API interface wrapper.
	 *
	 * @param string $name
	 * @param string $email
	 * @param string $key
	 * @since 0.1.0
	 */
	public function __construct(string $name, string $email, string $key)
	{
		$this->_email = $email;
		$this->_key = $key;
		$this->_name = $name;
	}

	/**
	 * Get categories.
	 *
	 * @since 0.1.0
	 * @return array|null
	 * @throws Exception
	 */
	public function categories(): ?array
	{
		return $this->_list_route('categories');
	}

	/**
	 * Get cards.
	 *
	 * @since 0.1.0
	 * @return array|null
	 * @throws Exception
	 */
	public function cards(): ?array
	{
		return $this->_list_route('credit_cards');
	}

	/**
	 * Get card invoices.
	 *
	 * @param CardModel|int $card
	 * @since 0.1.0
	 * @return array|null
	 * @throws Exception
	 */
	public function invoices($card): ?array
	{
		$id = $card instanceof CardModel ? $card->external_id : \intval($card);
		return $this->_list_route(\sprintf('credit_cards/%d/invoices', $id));
	}

	/**
	 * Get accounts.
	 *
	 * @since 0.1.0
	 * @return array|null
	 * @throws Exception
	 */
	public function accounts(): ?array
	{
		return $this->_list_route('accounts');
	}

	/**
	 * Get transactions.
	 *
	 * @param DateTimeImmutable $start_at
	 * @param DateTimeImmutable $end_at
	 * @since 0.1.0
	 * @return array|null
	 * @throws Exception
	 */
	public function transactions(DateTimeImmutable $start_at = null, DateTimeImmutable $end_at = null): ?array
	{
		$query = [];

		if ($start_at) {
			$query['start_date'] = $start_at->format('Y-m-d');
		}

		if ($end_at) {
			$query['end_date'] = $end_at->format('Y-m-d');
		}

		return $this->_list_route('transactions', $query);
	}

	/**
	 * List entities.
	 *
	 * @param string $route
	 * @param array|null $query
	 * @since 0.1.0
	 * @return array|null
	 * @throws Exception
	 */
	protected function _list_route(string $route, $query = null): ?array
	{
		/** @var \Illuminate\Http\Client\Response $response */
		$response = Http::withBasicAuth($this->_email, $this->_key)
			->withHeaders($this->_headers())
			->get($this->_url($route), $query);

		if ($response->failed()) {
			$err = \sprintf('Cannot connect to API route /%s', $route);
			Log::error($err);
			throw new Exception($err, $response->status());
		}

		if (empty($response->body())) {
			return null;
		}

		return $response->json();
	}

	/**
	 * Mount url with path.
	 *
	 * @param string $path
	 * @since 0.1.0
	 * @return string
	 */
	protected function _url(string $path): string
	{
		return \sprintf('%s/%s', $this->_baseUrl, \ltrim($path, '/'));
	}

	/**
	 * Get API headers.
	 *
	 * @since 0.1.0
	 * @return array
	 */
	protected function _headers(): array
	{
		return [
			'User-Agent' => \sprintf('%s (%s)', $this->_name, $this->_email)
		];
	}
}
