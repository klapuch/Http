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

final class StrictResponse extends Tester\TestCase {
	protected function htmlContentTypes() {
		return [
			[['CONTENT-TYPE' => 'text/html'], ['Content-Type' => 'text/html; utf-8']],
			[['cOntenT-type' => 'text/html'], ['Content-Type' => 'Text/html; Utf-8']],
			[['content-type' => 'text/html'], ['Content-Type' => 'Text/html;']],
			[['content-type' => 'text/html'], ['Content-Type' => 'text/html']],
			[['content-type' => 'text/html'], ['Content-Type' => 'Text/html']],
			[['content-type' => 'text/html'], ['CONTENT-TYPE' => 'Text/html']],
			[['Content-Type' => 'text/html'], ['content-type' => 'Text/html']],
			[['Content-Type' => 'text/html;utf-8'], ['content-type' => 'text/html;utf-8']],
		];
	}

	protected function nonHtmlContentTypes() {
		return [
			[['content-type' => 'text/html'], ['Content-Type' => 'text/css']],
			[['content-type' => 'text/html'], ['Content-Type' => 'html']],
			[['CONTENT-TYPE' => 'text/html'], ['Content-Type' => 'html/text']],
			[['content-type' => 'text/html'], ['Content-Type' => 'text/css;']],
			[['Content-Type' => 'text/html'], ['Content-Type' => 'utf-8']],
			[['content-type' => 'text/html'], ['Content-Type' => '']],
			[['content-type' => 'text/html'], []],
			[['Content-Type' => 'text/html;utf-8'], ['content-type' => 'text/html']],
		];
	}

	/**
	 * @dataProvider htmlContentTypes
	 */
	public function testAllowedContentTypes(array $header, array $headers) {
		Assert::same(
			'abc',
			(new Http\StrictResponse(
				$header,
				new Http\FakeResponse('abc', $headers)
			))->body()
		);
	}

	/**
	 * @dataProvider nonHtmlContentTypes
	 */
	public function testRefusedContentTypes(array $header, array $headers) {
		Assert::exception(
			function() use ($header, $headers) {
				(new Http\StrictResponse(
					$header,
					new Http\FakeResponse('abc', $headers)
				))->body();
			},
			\Throwable::class,
			'The response does not comply the strict header'
		);
	}

	public function testEmptyBodyWithWhiteCharacters() {
		Assert::exception(
			function() {
				(new Http\StrictResponse(
					[],
					new Http\FakeResponse('       ', [])
				))->body();
			},
			\Throwable::class,
			'The response does not comply the strict header'
		);
	}

	public function testEmptyBody() {
		Assert::exception(
			function() {
				(new Http\StrictResponse(
					[],
					new Http\FakeResponse('', [])
				))->body();
			},
			\Throwable::class,
			'The response does not comply the strict header'
		);
	}
}

(new StrictResponse())->run();
