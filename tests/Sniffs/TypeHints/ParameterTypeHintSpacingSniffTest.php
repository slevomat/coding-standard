<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use SlevomatCodingStandard\Sniffs\TestCase;

class ParameterTypeHintSpacingSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/parameterTypeHintSpacingNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/parameterTypeHintSpacingErrors.php');

		self::assertSame(12, $report->getErrorCount());

		self::assertSniffError(
			$report,
			3,
			ParameterTypeHintSpacingSniff::CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL,
			'There must be no whitespace between parameter type hint nullability symbol and parameter type hint of parameter $a.'
		);
		self::assertSniffError(
			$report,
			3,
			ParameterTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_TYPE_HINT_AND_PARAMETER,
			'There must be exactly one space between parameter type hint and parameter $a.'
		);
		self::assertSniffError(
			$report,
			3,
			ParameterTypeHintSpacingSniff::CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL,
			'There must be no whitespace between parameter type hint nullability symbol and parameter type hint of parameter $b.'
		);
		self::assertSniffError(
			$report,
			3,
			ParameterTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_TYPE_HINT_AND_PARAMETER,
			'There must be exactly one space between parameter type hint and reference sign of parameter $b.'
		);
		self::assertSniffError(
			$report,
			3,
			ParameterTypeHintSpacingSniff::CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL,
			'There must be no whitespace between parameter type hint nullability symbol and parameter type hint of parameter $c.'
		);
		self::assertSniffError(
			$report,
			3,
			ParameterTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_TYPE_HINT_AND_PARAMETER,
			'There must be exactly one space between parameter type hint and varadic parameter $c.'
		);

		self::assertSniffError(
			$report,
			16,
			ParameterTypeHintSpacingSniff::CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL,
			'There must be no whitespace between parameter type hint nullability symbol and parameter type hint of parameter $a.'
		);
		self::assertSniffError(
			$report,
			16,
			ParameterTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_TYPE_HINT_AND_PARAMETER,
			'There must be exactly one space between parameter type hint and parameter $a.'
		);
		self::assertSniffError(
			$report,
			16,
			ParameterTypeHintSpacingSniff::CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL,
			'There must be no whitespace between parameter type hint nullability symbol and parameter type hint of parameter $b.'
		);
		self::assertSniffError(
			$report,
			16,
			ParameterTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_TYPE_HINT_AND_PARAMETER,
			'There must be exactly one space between parameter type hint and reference sign of parameter $b.'
		);
		self::assertSniffError(
			$report,
			16,
			ParameterTypeHintSpacingSniff::CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL,
			'There must be no whitespace between parameter type hint nullability symbol and parameter type hint of parameter $c.'
		);
		self::assertSniffError(
			$report,
			16,
			ParameterTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_TYPE_HINT_AND_PARAMETER,
			'There must be exactly one space between parameter type hint and varadic parameter $c.'
		);
	}

	public function testFixableParameterTypeHintSpacingNoSpaceBetweenTypeHintAndParameter(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/fixableParameterTypeHintSpacingNoSpaceBetweenTypeHintAndParameter.php',
			[],
			[ParameterTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_TYPE_HINT_AND_PARAMETER]
		);
		self::assertAllFixedInFile($report);
	}

	public function testFixableParameterTypeHintSpacingMultipleSpacesBetweenTypeHintAndParameter(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/fixableParameterTypeHintSpacingMultipleSpacesBetweenTypeHintAndParameter.php',
			[],
			[ParameterTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_TYPE_HINT_AND_PARAMETER]
		);
		self::assertAllFixedInFile($report);
	}

	public function testFixableParameterTypeHintSpacingWhitespaceAfterNullabilitySymbol(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/fixableParameterTypeHintSpacingWhitespaceAfterNullabilitySymbol.php',
			[],
			[ParameterTypeHintSpacingSniff::CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL]
		);
		self::assertAllFixedInFile($report);
	}

}
