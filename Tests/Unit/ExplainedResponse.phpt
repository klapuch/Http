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

final class ExplainedResponse extends Tester\TestCase {
	public function testTransformingOnThrowing() {
		$response = new Http\ExplainedResponse(
			new Http\FakeResponse(null, null, null, new \DomainException('foo')),
			'Some error'
		);
		$ex = Assert::exception(function() use ($response) {
			$response->body();
		}, \DomainException::class, 'Some error');
		Assert::type(\DomainException::class, $ex->getPrevious());
		$ex = Assert::exception(function() use ($response) {
			$response->headers();
		}, \DomainException::class, 'Some error');
		Assert::type(\DomainException::class, $ex->getPrevious());
		$ex = Assert::exception(function() use ($response) {
			$response->code();
		}, \DomainException::class, 'Some error');
		Assert::type(\DomainException::class, $ex->getPrevious());
	}

	public function testBehavingAsUsualWithoutThrowing() {
		$response = new Http\ExplainedResponse(
			new Http\FakeResponse('', [], 0),
			'Some error'
		);
		Assert::noError(function() use ($response) {
			$response->body();
		});
		Assert::noError(function() use ($response) {
			$response->headers();
		});
		Assert::noError(function() use ($response) {
			$response->code();
		});
	}
}

(new ExplainedResponse())->run();