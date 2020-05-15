<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use SlevomatCodingStandard\Sniffs\TestCase;

class ReturnTypeHintSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/returnTypeHintNoErrors.php', [
			'traversableTypeHints' => ['Traversable'],
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/returnTypeHintErrors.php', [
			'enableObjectTypeHint' => true,
			'traversableTypeHints' => ['Traversable'],
		]);

		self::assertSame(40, $report->getErrorCount());

		self::assertSniffError($report, 6, ReturnTypeHintSniff::CODE_MISSING_ANY_TYPE_HINT);
		self::assertSniffError($report, 14, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 22, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 30, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 38, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 46, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 54, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 60, ReturnTypeHintSniff::CODE_USELESS_ANNOTATION);
		self::assertSniffError($report, 67, ReturnTypeHintSniff::CODE_MISSING_TRAVERSABLE_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 73, ReturnTypeHintSniff::CODE_MISSING_TRAVERSABLE_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 80, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 88, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 92, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 99, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 105, ReturnTypeHintSniff::CODE_MISSING_TRAVERSABLE_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 107, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 113, ReturnTypeHintSniff::CODE_MISSING_TRAVERSABLE_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 121, ReturnTypeHintSniff::CODE_MISSING_TRAVERSABLE_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 129, ReturnTypeHintSniff::CODE_MISSING_TRAVERSABLE_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 131, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 137, ReturnTypeHintSniff::CODE_MISSING_TRAVERSABLE_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 139, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 145, ReturnTypeHintSniff::CODE_MISSING_TRAVERSABLE_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 147, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 155, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 163, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 170, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 177, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 184, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 191, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 198, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 202, ReturnTypeHintSniff::CODE_USELESS_ANNOTATION);

		self::assertSniffError($report, 208, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 213, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 218, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 223, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 228, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 233, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 238, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);

		self::assertSniffError($report, 243, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);

		self::assertAllFixedInFile($report);
	}

}
