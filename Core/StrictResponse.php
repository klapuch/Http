<?php
declare(strict_types = 1);
namespace Klapuch\Http;

/**
 * Response which must accomplish one special strict header
 */
final class StrictResponse implements Response {
    private $header;
    private $origin;

    public function __construct(array $header, Response $origin) {
        $this->header = $header;
        $this->origin = $origin;
    }

    public function body(): string {
        if(strlen(trim($this->origin->body())) && !$this->violated())
            return $this->origin->body();
        throw new \Exception(
            'The response does not accomplish the strict header'
        );
    }

    public function headers(): array {
        return $this->origin->headers();
    }

    public function code(): int {
        return $this->origin->code();
    }

    /**
     * Is the strict header violated?
     * @return bool
     */
    private function violated(): bool {
        $headers = array_change_key_case($this->origin->headers(), CASE_LOWER);
        $contentType = $headers[strtolower(key($this->header))] ?? '';
        if(!empty($contentType)) {
            if($contentType !== current($this->header)) {
                foreach(explode(';', $contentType) as $value)
                    if(strcasecmp($value, current($this->header)) === 0)
                        return false;
                return true;
            }
            return false;
        }
        return true;
    }
}
