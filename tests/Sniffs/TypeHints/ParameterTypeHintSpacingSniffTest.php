<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

class ParameterTypeHintSpacingSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testNoErrors(): void
	{
		$this->assertNoSniffErrorInFile($this->checkFile(__DIR__ . '/data/parameterTypeHintSpacingNoErrors.php'));
	}

	public function testErrors(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/parameterTypeHintSpacingErrors.php');

		$this->assertSame(6, $report->getErrorCount());

		$this->assertSniffError($report, 3, ParameterTypeHintSpacingSniff::CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL, 'There must be no whitespace between parameter type hint nullability symbol and parameter type hint of parameter $a.');
		$this->assertSniffError($report, 3, ParameterTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_TYPE_HINT_AND_PARAMETER, 'There must be exactly one space between parameter type hint and parameter $a.');
		$this->assertSniffError($report, 3, ParameterTypeHintSpacingSniff::CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL, 'There must be no whitespace between parameter type hint nullability symbol and parameter type hint of parameter $b.');
		$this->assertSniffError($report, 3, ParameterTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_TYPE_HINT_AND_PARAMETER, 'There must be exactly one space between parameter type hint and reference sign of parameter $b.');
		$this->assertSniffError($report, 3, ParameterTypeHintSpacingSniff::CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL, 'There must be no whitespace between parameter type hint nullability symbol and parameter type hint of parameter $c.');
		$this->assertSniffError($report, 3, ParameterTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_TYPE_HINT_AND_PARAMETER, 'There must be exactly one space between parameter type hint and varadic parameter $c.');
	}

	public function testFixableParameterTypeHintSpacingNoSpaceBetweenTypeHintAndParameter(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/fixableParameterTypeHintSpacingNoSpaceBetweenTypeHintAndParameter.php', [], [ParameterTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_TYPE_HINT_AND_PARAMETER]);
		$this->assertAllFixedInFile($report);
	}

	public function testFixableParameterTypeHintSpacingMultipleSpacesBetweenTypeHintAndParameter(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/fixableParameterTypeHintSpacingMultipleSpacesBetweenTypeHintAndParameter.php', [], [ParameterTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_TYPE_HINT_AND_PARAMETER]);
		$this->assertAllFixedInFile($report);
	}

	public function testFixableParameterTypeHintSpacingWhitespaceAfterNullabilitySymbol(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/fixableParameterTypeHintSpacingWhitespaceAfterNullabilitySymbol.php', [], [ParameterTypeHintSpacingSniff::CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL]);
		$this->assertAllFixedInFile($report);
	}

}
