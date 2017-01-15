<?php
/**
 * @testCase
 * @phpVersion > 7.0.0
 */
namespace Klapuch\Http\Integration;

use Klapuch\Http;
use Tester;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

final class RawResponse extends Tester\TestCase {
	public function testBody() {
		Assert::same('abc', (new Http\RawResponse([], 'abc'))->body());
	}

	public function testEmptyBodyWithoutError() {
		Assert::same('', (new Http\RawResponse([], ''))->body());
	}

	public function testGrabbingStatusCodeFromHeaders() {
		Assert::same(
			404,
			(new Http\RawResponse(['HTTP/1.0 404 Not Found'], 'abc'))->code()
		);
		Assert::same(
			200,
			(new Http\RawResponse(['http/1.0 200 OK'], 'abc'))->code()
		);
	}

	/**
	 * @throws \Exception Status code of the response is not known
	 */
	public function testThrowingOnLostStatusCode() {
		(new Http\RawResponse(['Content-Length: 666'], 'abc'))->code();
	}

	/**
	 * @throws \Exception Allowed range for the status codes is 1xx - 5xx
	 */
	public function testThrowingOnUnknownStatusCode() {
		(new Http\RawResponse(['HTTP/1.0 999 Not Found'], 'abc'))->code();
	}

	public function testTransformingToKeyValueHeaders() {
		Assert::same(
			['X-Powered-By' => 'PHP', 'Content-Type' => 'text/html', 'X' => '666'],
			(new Http\RawResponse(
				['X-Powered-By: PHP', 'Content-Type:text/html', 'foo', 'X:  666'],
				'abc'
			))->headers()
		);
	}

	/**
	 * @throws \Exception Headers of the response are empty
	 */
	public function testThrowingOnEmptyHeaders() {
		(new Http\RawResponse([], 'abc'))->headers();
	}

	/**
	 * @throws \Exception Headers of the response are empty
	 */
	public function testThrowingOnInvalidHeadersConsideredAsEmpty() {
		(new Http\RawResponse(['this is not a header'], 'abc'))->headers();
	}
}

(new RawResponse())->run();