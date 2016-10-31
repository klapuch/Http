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
     * @throws \InvalidArgumentException Supported methods are GET, POST - "foo" given
     */
    public function testUnknownMethodWithError() {
        (new Http\BasicRequest('foo', new Uri\FakeUri()))->send();
    }

    public function testHttpResponseWithoutError() {
        Assert::noError(function() {
            $url = 'http://www.example.com';
            (new Http\BasicRequest(
                'get',
                new Uri\FakeUri($url)
            ))->send();
        });
    }

    public function testHttpsResponseWithoutError() {
        Assert::noError(function() {
            $url = 'https://www.google.com';
            (new Http\BasicRequest(
                'get',
                new Uri\FakeUri($url)
            ))->send();
        });
    }

    public function testPriorDefaultOptions() {
        Assert::noError(function() {
            $url = 'https://www.google.com';
            (new Http\BasicRequest(
                'GET',
                new Uri\FakeUri($url),
                [CURLOPT_URL => 'http://404.php.net/']
            ))->send();
        });
    }

    public function testGetRequestWithFields() {
        Assert::noError(function() {
            $url = 'https://www.google.com';
            (new Http\BasicRequest(
                'GET',
                new Uri\FakeUri($url)
            ))->send('abc');
        });
    }

    /**
     * @throws \Exception
     */
    public function testErrorDuringRequesting() {
        $url = 'http://404.php.net/';
        (new Http\BasicRequest(
            'get',
            new Uri\FakeUri($url)
        ))->send();
    }
}

(new BasicRequest())->run();
