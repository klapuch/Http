<?php
declare(strict_types = 1);
/**
 * @testCase
 * @phpVersion > 7.1
 */
namespace Klapuch\Http\Unit;

use Klapuch\Http;
use Tester;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

final class AvailableResponse extends Tester\TestCase {
	public function testAvailableResponse() {
		Assert::same(
			'abc',
			(new Http\AvailableResponse(
				new Http\FakeResponse('abc', [], 200)
			))->body()
		);
	}

	/**
	 * @throws \UnexpectedValueException The response is not available
	 */
	public function testThrowingOnNotAvailableResponse() {
		(new Http\AvailableResponse(
			new Http\FakeResponse('abc', [], 404)
		))->body();
	}
}

(new AvailableResponse())->run();