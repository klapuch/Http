<?php
declare(strict_types = 1);
namespace Klapuch\Http;

/**
 * Response which must comply one special strict header
 */
final class StrictResponse implements Response {
    const DELIMITER = ';';
    private $header;
    private $origin;

    public function __construct(array $header, Response $origin) {
        $this->header = $header;
        $this->origin = $origin;
    }

    public function body(): string {
        if($this->complied())
            return $this->origin->body();
        throw new \Exception(
            'The response does not comply the strict header'
        );
    }

    public function headers(): array {
        return $this->origin->headers();
    }

    public function code(): int {
        return $this->origin->code();
    }

    /**
     * Are the requirements complied?
     * @return bool
     */
    private function complied(): bool {
        list($field, $value) = [key($this->header), current($this->header)];
        return trim($this->origin->body()) && !$this->violated($field, $value);
    }

    /**
     * Is the strict header violated?
     * @param string field
     * @param string value
     * @return bool
     */
    private function violated(string $field, string $value): bool {
        $headers = array_change_key_case($this->origin->headers(), CASE_LOWER);
        $contentType = $headers[strtolower($field)] ?? '';
        if(!empty($contentType)) {
            if($contentType !== $value) {
                foreach(explode(self::DELIMITER, $contentType) as $part)
                    if(strcasecmp($part, $value) === 0)
                        return false;
                return true;
            }
            return false;
        }
        return true;
    }
}