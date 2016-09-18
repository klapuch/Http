<?php
/**
 * @testCase
 * @phpVersion > 7.0.0
 */
namespace Klapuch\Http\Integration;

use Klapuch\{
    Http, Uri
};
use Tester;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

final class BasicRequest extends Tester\TestCase {
    /**
     * @throws \InvalidArgumentException Supported methods are GET - "foo" given
     */
    public function testUnknownMethodWithError() {
        (new Http\BasicRequest('foo', new Uri\FakeUri()))->send();
    }

    public function testHttpResponseWithoutError() {
        Assert::noError(function() {
            $url = 'http://localhost';
            $response = (new Http\BasicRequest(
                'get',
                new Uri\FakeUri($url)
            ))->send();
        });
    }

    public function testHttpsResponseWithoutError() {
        Assert::noError(function() {
            $url = 'https://www.google.com';
            $response = (new Http\BasicRequest(
                'get',
                new Uri\FakeUri($url)
            ))->send();
        });
    }

    public function testPriorDefaultOptions() {
        Assert::noError(function() {
            $url = 'https://www.google.com';
            $response = (new Http\BasicRequest(
                'GET',
                new Uri\FakeUri($url),
                [CURLOPT_URL => 'http://404.php.net/']
            ))->send();
        });
    }

    /**
     * @throws \Exception Could not resolve host: 404.php.net
     */
    public function testErrorDuringRequesting() {
        $url = 'http://404.php.net/';
        $response = (new Http\BasicRequest(
            'get',
            new Uri\FakeUri($url)
        ))->send();
    }
}

(new BasicRequest())->run();
