<?php
declare(strict_types = 1);
namespace Klapuch\Http;

use Klapuch\Uri;

/**
 * Basic HTTP request
 */
final class BasicRequest implements Request {
    const METHODS = [
        'GET',
        'POST',
    ];
    const HEADER = 0;
    const BODY = 1;
    private $method;
    private $uri;
    private $options;

    public function __construct(
        string $method,
        Uri\Uri $uri,
        array $options = []
    ) {
        $this->method = $method;
        $this->uri = $uri;
        $this->options = $options;
    }

    public function send(): Response {
        if(!$this->supported()) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Supported methods are %s - "%s" given',
                    $this->readableMethods(),
                    $this->method
                )
            );
        }
        return new RawResponse(...$this->response());
    }

    /**
     * Is the request supported?
     * @return bool
     */
    private function supported(): bool {
        return in_array(strtoupper($this->method), self::METHODS);
    }

    /**
     * Human readable supported methods
     * @return string
     */
    private function readableMethods(): string {
        return implode(', ', self::METHODS);
    }

    /**
     * Response given from the requested source
     * @throws \Exception
     * @return array
     */
    private function response(): array {
        $curl = curl_init();
        $defaultOptions = [
            CURLOPT_URL => $this->uri->reference(),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_AUTOREFERER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_CUSTOMREQUEST => $this->method,
        ];
        curl_setopt_array($curl, $defaultOptions + $this->options); 
        $body = curl_exec($curl);
        if($body === false)
            throw new \Exception(curl_error($curl));
        curl_close($curl);
        return [
            self::HEADER => get_headers($this->uri->reference()),
            self::BODY => $body,
        ];
    }
}
