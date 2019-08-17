<?php
declare(strict_types = 1);
namespace Klapuch\Http;

/**
 * Basic HTTP request
 */
final class BasicRequest implements Request {
	private $method;
	private $uri;
	private $options;
	private $body;

	public function __construct(
		string $method,
		string $uri,
		array $options = [],
		string $body = ''
	) {
		$this->method = $method;
		$this->uri = $uri;
		$this->options = $options;
		$this->body = $body;
	}

	public function send(): Response {
		return new RawResponse(...$this->response($this->body));
	}

	/**
	 * Response given from the requested source
	 * @param string $fields
	 * @throws \Exception
	 * @return array
	 */
	private function response(string $fields): array {
		$curl = curl_init();
		try {
			curl_setopt_array(
				$curl,
				[
					CURLOPT_URL => $this->uri,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_CUSTOMREQUEST => strtoupper($this->method),
					CURLOPT_POSTFIELDS => $fields,
				] + $this->options
			);
			$responseHeaders = [];
			curl_setopt($curl, CURLOPT_HEADERFUNCTION, static function ($curl, string $header) use (&$responseHeaders): int {
				$responseHeaders[curl_getinfo($curl, CURLINFO_REDIRECT_COUNT)][] = $header;
				return strlen($header);
			});
			$body = curl_exec($curl);
			if ($body === false)
				throw new \UnexpectedValueException(curl_error($curl), curl_errno($curl));
			return [end($responseHeaders) ?: [], $body];
		} finally {
			curl_close($curl);
		}
	}
}
