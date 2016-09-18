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
     * @throws \Exception The response is not available
     */
    public function testNotAvailableResponse() {
        (new Http\AvailableResponse(
            new Http\FakeResponse('abc', [], 404)
        ))->body();
    }
}

(new AvailableResponse())->run();
