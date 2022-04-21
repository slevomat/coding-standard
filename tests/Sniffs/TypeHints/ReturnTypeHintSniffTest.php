<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use SlevomatCodingStandard\Sniffs\TestCase;

class ReturnTypeHintSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/returnTypeHintNoErrors.php', [
			'enableMixedTypeHint' => true,
			'enableUnionTypeHint' => false,
			'enableNeverTypeHint' => false,
			'traversableTypeHints' => ['Traversable', '\ArrayIterator'],
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/returnTypeHintErrors.php', [
			'enableObjectTypeHint' => true,
			'enableStaticTypeHint' => true,
			'enableMixedTypeHint' => true,
			'enableUnionTypeHint' => false,
			'enableNeverTypeHint' => false,
			'traversableTypeHints' => ['Traversable', '\ArrayIterator'],
		]);

		self::assertSame(57, $report->getErrorCount());

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
		self::assertSniffError($report, 248, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 253, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 258, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);

		self::assertSniffError($report, 266, ReturnTypeHintSniff::CODE_USELESS_SUPPRESS);
		self::assertSniffError($report, 274, ReturnTypeHintSniff::CODE_USELESS_SUPPRESS);
		self::assertSniffError($report, 282, ReturnTypeHintSniff::CODE_USELESS_SUPPRESS);
		self::assertSniffError($report, 290, ReturnTypeHintSniff::CODE_USELESS_SUPPRESS);
		self::assertSniffError($report, 298, ReturnTypeHintSniff::CODE_USELESS_SUPPRESS);

		self::assertSniffError($report, 305, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 313, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 318, ReturnTypeHintSniff::CODE_USELESS_ANNOTATION);

		self::assertSniffError($report, 326, ReturnTypeHintSniff::CODE_MISSING_TRAVERSABLE_TYPE_HINT_SPECIFICATION);

		self::assertSniffError($report, 333, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 338, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 343, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 348, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 353, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);

		self::assertAllFixedInFile($report);
	}

	public function testWithUnionNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/returnTypeHintWithUnionNoErrors.php', [
			'enableObjectTypeHint' => true,
			'enableMixedTypeHint' => true,
			'enableUnionTypeHint' => true,
			'enableNeverTypeHint' => false,
			'traversableTypeHints' => ['Traversable', '\ArrayIterator'],
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testWithUnionErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/returnTypeHintWithUnionErrors.php', [
			'enableObjectTypeHint' => true,
			'enableMixedTypeHint' => true,
			'enableUnionTypeHint' => true,
			'enableNeverTypeHint' => false,
			'traversableTypeHints' => ['Traversable', '\ArrayIterator'],
		]);

		self::assertSame(16, $report->getErrorCount());

		self::assertSniffError($report, 7, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 11, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 15, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 19, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 23, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 27, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 31, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 35, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 38, ReturnTypeHintSniff::CODE_USELESS_ANNOTATION);

		self::assertSniffError($report, 43, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 47, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 51, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 55, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 59, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 63, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 67, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);

		self::assertAllFixedInFile($report);
	}

	public function testWithNeverErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/returnTypeHintWithNeverErrors.php', [
			'enableObjectTypeHint' => true,
			'enableMixedTypeHint' => false,
			'enableUnionTypeHint' => false,
			'enableNeverTypeHint' => true,
		]);

		self::assertSame(4, $report->getErrorCount());

		self::assertSniffError($report, 7, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 13, ReturnTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 19, ReturnTypeHintSniff::CODE_LESS_SPECIFIC_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 25, ReturnTypeHintSniff::CODE_LESS_SPECIFIC_NATIVE_TYPE_HINT);

		self::assertAllFixedInFile($report);
	}

}
