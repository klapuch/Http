<?php
declare(strict_types = 1);
namespace Klapuch\Http;

/**
 * Fake
 */
final class FakeResponse implements Response {
    private $body;
    private $headers;
    private $code;

    public function __construct($body = null, $headers = null, $code = null) {
        $this->body = $body;
        $this->headers = $headers;
        $this->code = $code;
    }

    public function body(): string {
        return $this->body;
    }

    public function headers(): array {
        return $this->headers;
    }

    public function code(): int {
        return $this->code;
    }
}
