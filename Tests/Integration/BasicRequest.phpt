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
    public function testUnknownMethod() {
        (new Http\BasicRequest('foo', new Uri\FakeUri()))->send();
    }

    public function testHttpResponse() {
        Assert::noError(function() {
            $url = 'http://www.example.com';
            (new Http\BasicRequest(
                'GET',
                new Uri\FakeUri($url)
            ))->send();
        });
    }

    public function testHttpsResponse() {
        Assert::noError(function() {
            $url = 'https://www.google.com';
            (new Http\BasicRequest(
                'GET',
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
            $url = 'https://httpbin.org/get';
            (new Http\BasicRequest(
                'GET',
                new Uri\FakeUri($url)
            ))->send('abc');
        });
    }

    public function testCaseInsensitiveGet() {
		$url = 'https://httpbin.org/get';
		$response = (new Http\BasicRequest(
			'get',
			new Uri\FakeUri($url)
		))->send();
		Assert::contains('"headers": {', $response->body());
    }

	public function testCaseInsensitivePost() {
		$url = 'https://httpbin.org/post';
		$response = (new Http\BasicRequest(
			'post',
			new Uri\FakeUri($url)
		))->send();
		Assert::contains('"data": ""', $response->body());
    }

    public function testPassedPostData() {
		$url = 'https://httpbin.org/post';
		$response = (new Http\BasicRequest(
			'POST',
			new Uri\FakeUri($url)
		))->send('name=Dominik');
		Assert::contains('"name": "Dominik"', $response->body());
    }

    /**
     * @throws \Exception
     */
    public function testErrorDuringRequesting() {
        $url = 'http://404.php.net/';
        (new Http\BasicRequest(
            'GET',
            new Uri\FakeUri($url)
        ))->send();
    }
}

(new BasicRequest())->run();
