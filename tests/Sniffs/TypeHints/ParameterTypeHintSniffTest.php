<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use SlevomatCodingStandard\Sniffs\TestCase;

class ParameterTypeHintSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/parameterTypeHintNoErrors.php', [
			'traversableTypeHints' => ['Traversable'],
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/parameterTypeHintErrors.php', [
			'enableObjectTypeHint' => true,
			'traversableTypeHints' => ['Traversable'],
		]);

		self::assertSame(38, $report->getErrorCount());

		self::assertSniffError($report, 6, ParameterTypeHintSniff::CODE_MISSING_ANY_TYPE_HINT);
		self::assertSniffError($report, 14, ParameterTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 22, ParameterTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 30, ParameterTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 38, ParameterTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 45, ParameterTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 53, ParameterTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 61, ParameterTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 67, ParameterTypeHintSniff::CODE_USELESS_ANNOTATION);
		self::assertSniffError($report, 74, ParameterTypeHintSniff::CODE_MISSING_TRAVERSABLE_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 80, ParameterTypeHintSniff::CODE_MISSING_TRAVERSABLE_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 90, ParameterTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 96, ParameterTypeHintSniff::CODE_MISSING_TRAVERSABLE_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 98, ParameterTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 104, ParameterTypeHintSniff::CODE_MISSING_TRAVERSABLE_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 112, ParameterTypeHintSniff::CODE_MISSING_TRAVERSABLE_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 120, ParameterTypeHintSniff::CODE_MISSING_TRAVERSABLE_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 122, ParameterTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 127, ParameterTypeHintSniff::CODE_MISSING_TRAVERSABLE_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 129, ParameterTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 134, ParameterTypeHintSniff::CODE_MISSING_TRAVERSABLE_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 136, ParameterTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 143, ParameterTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 150, ParameterTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 157, ParameterTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 164, ParameterTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 171, ParameterTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 178, ParameterTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 185, ParameterTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 189, ParameterTypeHintSniff::CODE_USELESS_ANNOTATION);

		self::assertSniffError($report, 195, ParameterTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 200, ParameterTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 205, ParameterTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 210, ParameterTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 215, ParameterTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 220, ParameterTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 225, ParameterTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);

		self::assertSniffError($report, 230, ParameterTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);

		self::assertAllFixedInFile($report);
	}

}
