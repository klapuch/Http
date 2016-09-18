<?php
declare(strict_types = 1);
namespace Klapuch\Http;

/**
 * Available HTTP response with status code lower than 4xx
 */
final class AvailableResponse implements Response {
    private $origin;

    public function __construct(Response $origin) {
        $this->origin = $origin;
    }

    public function body(): string {
        if($this->available())
            return $this->origin->body();
        throw new \Exception('The response is not available');
    }

    public function headers(): array {
        return $this->origin->headers();
    }

    public function code(): int {
        return $this->origin->code();
    }

    private function available(): bool {
        return $this->code() < 400;
    }
}
