<?php
declare(strict_types = 1);
/**
 * @testCase
 * @phpVersion > 7.1
 */
namespace Klapuch\Http\Integration;

use Klapuch\Http;
use Klapuch\Uri\FakeUri;
use Tester;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

final class BasicRequest extends Tester\TestCase {
	public function testHttpResponse() {
		Assert::noError(
			function() {
				$url = new FakeUri('http://www.example.com');
				(new Http\BasicRequest(
					'GET',
					$url
				))->send();
			}
		);
	}

	public function testKeyValueHeaders() {
		$url = new FakeUri('http://www.example.com');
		$response = (new Http\BasicRequest(
			'GET',
			$url
		))->send();
		$headers = $response->headers();
		Assert::same('text/html; charset=UTF-8', $headers['Content-Type']);
	}

	public function testHttpsResponse() {
		Assert::noError(
			function() {
				$url = new FakeUri('https://www.google.com');
				(new Http\BasicRequest(
					'GET',
					$url
				))->send();
			}
		);
	}

	public function testPriorToDefaultOptions() {
		Assert::noError(
			function() {
				$url = new FakeUri('https://www.google.com');
				(new Http\BasicRequest(
					'GET',
					$url,
					[CURLOPT_URL => 'http://404.php.net/']
				))->send();
			}
		);
	}

	public function testGetRequestWithFields() {
		Assert::noError(
			function() {
				$url = new FakeUri('https://httpbin.org/get');
				(new Http\BasicRequest(
					'GET',
					$url,
					[],
					'abc'
				))->send();
			}
		);
	}

	public function testCaseInsensitiveGet() {
		$url = new FakeUri('https://httpbin.org/get');
		$response = (new Http\BasicRequest(
			'get',
			$url
		))->send();
		Assert::contains('"headers": {', $response->body());
	}

	public function testCaseInsensitivePost() {
		$url = new FakeUri('https://httpbin.org/post');
		$response = (new Http\BasicRequest(
			'post',
			$url
		))->send();
		Assert::contains('"data": ""', $response->body());
	}

	public function testPassedPostData() {
		$url = new FakeUri('https://httpbin.org/post');
		$response = (new Http\BasicRequest(
			'POST',
			$url,
			[],
			'name=Dominik'
		))->send();
		Assert::contains('"name": "Dominik"', $response->body());
	}

	/**
	 * @throws \UnexpectedValueException
	 */
	public function testErrorDuringRequesting() {
		$url = new FakeUri('http://404.php.net/');
		(new Http\BasicRequest(
			'GET',
			$url
		))->send();
	}
}

(new BasicRequest())->run();
