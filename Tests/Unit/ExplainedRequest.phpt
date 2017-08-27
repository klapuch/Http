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

final class ExplainedRequest extends Tester\TestCase {
	public function testTransformingOnThrowing() {
		$ex = Assert::exception(function() {
			(new Http\ExplainedRequest(
				new Http\FakeRequest(null, new \DomainException('foo')),
				'Some error'
			))->send();
		}, \DomainException::class, 'Some error');
		Assert::type(\DomainException::class, $ex->getPrevious());
	}

	public function testBehavingAsUsualWithoutThrowing() {
		Assert::noError(function() {
			(new Http\ExplainedRequest(
				new Http\FakeRequest(new Http\FakeResponse()),
				'Some error'
			))->send();
		});
	}
}

(new ExplainedRequest())->run();