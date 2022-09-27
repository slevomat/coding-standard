<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use SlevomatCodingStandard\Sniffs\TestCase;

class ParameterTypeHintSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/parameterTypeHintNoErrors.php', [
			'enableMixedTypeHint' => true,
			'enableUnionTypeHint' => false,
			'enableIntersectionTypeHint' => false,
			'enableStandaloneNullTrueFalseTypeHints' => false,
			'traversableTypeHints' => ['Traversable', '\ArrayIterator'],
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/parameterTypeHintErrors.php', [
			'enableObjectTypeHint' => true,
			'enableMixedTypeHint' => true,
			'enableUnionTypeHint' => false,
			'enableIntersectionTypeHint' => false,
			'enableStandaloneNullTrueFalseTypeHints' => false,
			'traversableTypeHints' => ['Traversable', '\ArrayIterator'],
		]);

		self::assertSame(50, $report->getErrorCount());

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
		self::assertSniffError($report, 235, ParameterTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 240, ParameterTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);

		self::assertSniffError($report, 248, ParameterTypeHintSniff::CODE_USELESS_SUPPRESS);
		self::assertSniffError($report, 256, ParameterTypeHintSniff::CODE_USELESS_SUPPRESS);
		self::assertSniffError($report, 264, ParameterTypeHintSniff::CODE_USELESS_SUPPRESS);
		self::assertSniffError($report, 272, ParameterTypeHintSniff::CODE_USELESS_SUPPRESS);
		self::assertSniffError($report, 280, ParameterTypeHintSniff::CODE_USELESS_SUPPRESS);

		self::assertSniffError($report, 285, ParameterTypeHintSniff::CODE_USELESS_ANNOTATION);
		self::assertSniffError($report, 292, ParameterTypeHintSniff::CODE_MISSING_ANY_TYPE_HINT);

		self::assertSniffError($report, 301, ParameterTypeHintSniff::CODE_MISSING_TRAVERSABLE_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 303, ParameterTypeHintSniff::CODE_USELESS_ANNOTATION);
		self::assertSniffError($report, 304, ParameterTypeHintSniff::CODE_USELESS_ANNOTATION);

		self::assertAllFixedInFile($report);
	}

	public function testWithUnionNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/parameterTypeHintWithUnionNoErrors.php', [
			'enableObjectTypeHint' => true,
			'enableMixedTypeHint' => true,
			'enableUnionTypeHint' => true,
			'enableIntersectionTypeHint' => false,
			'enableStandaloneNullTrueFalseTypeHints' => false,
			'traversableTypeHints' => ['Traversable', '\ArrayIterator'],
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testWithUnionErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/parameterTypeHintWithUnionErrors.php', [
			'enableObjectTypeHint' => true,
			'enableMixedTypeHint' => true,
			'enableUnionTypeHint' => true,
			'enableIntersectionTypeHint' => false,
			'enableStandaloneNullTrueFalseTypeHints' => false,
			'traversableTypeHints' => ['Traversable', '\ArrayIterator'],
		]);

		self::assertSame(14, $report->getErrorCount());

		self::assertSniffError($report, 7, ParameterTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 11, ParameterTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 15, ParameterTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 19, ParameterTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 23, ParameterTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 27, ParameterTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 31, ParameterTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 35, ParameterTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 39, ParameterTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);

		self::assertSniffError($report, 43, ParameterTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 47, ParameterTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 51, ParameterTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 55, ParameterTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 59, ParameterTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);

		self::assertAllFixedInFile($report);
	}

	public function testWithIntersectionNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/parameterTypeHintWithIntersectionNoErrors.php', [
			'enableObjectTypeHint' => true,
			'enableMixedTypeHint' => true,
			'enableUnionTypeHint' => false,
			'enableIntersectionTypeHint' => false,
			'enableStandaloneNullTrueFalseTypeHints' => false,
			'traversableTypeHints' => ['Traversable', '\ArrayIterator'],
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testWithIntersectionErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/parameterTypeHintWithIntersectionErrors.php', [
			'enableObjectTypeHint' => true,
			'enableMixedTypeHint' => true,
			'enableUnionTypeHint' => false,
			'enableIntersectionTypeHint' => true,
			'enableStandaloneNullTrueFalseTypeHints' => false,
			'traversableTypeHints' => ['Traversable', '\ArrayIterator'],
		]);

		self::assertSame(2, $report->getErrorCount());

		self::assertSniffError($report, 9, ParameterTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 14, ParameterTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);

		self::assertAllFixedInFile($report);
	}

	public function testWithNullTrueFalseErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/parameterTypeHintWithNullTrueFalseErrors.php', [
			'enableObjectTypeHint' => true,
			'enableMixedTypeHint' => true,
			'enableUnionTypeHint' => true,
			'enableIntersectionTypeHint' => true,
			'enableStandaloneNullTrueFalseTypeHints' => true,
			'traversableTypeHints' => ['Traversable', '\ArrayIterator'],
		]);

		self::assertSame(6, $report->getErrorCount());

		self::assertSniffError($report, 7, ParameterTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 13, ParameterTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 19, ParameterTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 22, ParameterTypeHintSniff::CODE_USELESS_ANNOTATION);
		self::assertSniffError($report, 27, ParameterTypeHintSniff::CODE_USELESS_ANNOTATION);
		self::assertSniffError($report, 33, ParameterTypeHintSniff::CODE_USELESS_ANNOTATION);

		self::assertAllFixedInFile($report);
	}

}
