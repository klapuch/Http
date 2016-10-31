<?php
declare(strict_types = 1);
namespace Klapuch\Http;

interface Request {
	/**
	 * Sent the request and receive response as a feedback
	 * @throws \Exception
	 * @param string $body
	 * @return Response
	 */
	public function send(string $body = ''): Response;
}
