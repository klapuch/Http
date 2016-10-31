<?php
declare(strict_types = 1);
namespace Klapuch\Http;

/**
 * Fake
 */
final class FakeRequest implements Request {
    private $response;

    public function __construct(Response $response = null) {
        $this->response = $response;
    }

    public function send(string $body = ''): Response {
        return $this->response;
    }
}
