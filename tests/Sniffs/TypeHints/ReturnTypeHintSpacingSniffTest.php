<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

class ReturnTypeHintSpacingSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testCorrectSpacing(): void
	{
		$this->assertNoSniffErrorInFile($this->checkFile(__DIR__ . '/data/returnTypeHintsCorrect.php'));
	}

	public function testIncorrectSpacing(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/returnTypeHintsIncorrect.php');
		$this->assertSniffError(
			$report,
			3,
			ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE_HINT
		);
		$this->assertSniffError(
			$report,
			8,
			ReturnTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_TYPE_HINT
		);
		$this->assertSniffError(
			$report,
			13,
			ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON
		);
		$this->assertSniffError(
			$report,
			13,
			ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE_HINT
		);
		$this->assertSniffError(
			$report,
			18,
			ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON
		);
		$this->assertSniffError(
			$report,
			23,
			ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE_HINT
		);
		$this->assertSniffError(
			$report,
			28,
			ReturnTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_TYPE_HINT
		);
		$this->assertSniffError(
			$report,
			33,
			ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON
		);
		$this->assertSniffError(
			$report,
			33,
			ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE_HINT
		);
		$this->assertSniffError(
			$report,
			38,
			ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON
		);
		$this->assertSniffError(
			$report,
			75,
			ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE_HINT
		);
		$this->assertSniffError(
			$report,
			80,
			ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE_HINT
		);
		$this->assertSniffError(
			$report,
			85,
			ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE_HINT
		);
		$this->assertSniffError(
			$report,
			90,
			ReturnTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_TYPE_HINT
		);
		$this->assertSniffError(
			$report,
			95,
			ReturnTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_TYPE_HINT
		);
		$this->assertSniffError(
			$report,
			100,
			ReturnTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_TYPE_HINT
		);
		$this->assertSniffError(
			$report,
			105,
			ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON
		);
		$this->assertSniffError(
			$report,
			105,
			ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE_HINT
		);
		$this->assertSniffError(
			$report,
			110,
			ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON
		);
		$this->assertSniffError(
			$report,
			110,
			ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE_HINT
		);
		$this->assertSniffError(
			$report,
			115,
			ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON
		);
		$this->assertSniffError(
			$report,
			115,
			ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE_HINT
		);
		$this->assertSniffError(
			$report,
			120,
			ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON
		);
		$this->assertSniffError(
			$report,
			120,
			ReturnTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_TYPE_HINT
		);
		$this->assertSniffError(
			$report,
			125,
			ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON
		);
		$this->assertSniffError(
			$report,
			125,
			ReturnTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_TYPE_HINT
		);
		$this->assertSniffError(
			$report,
			130,
			ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON
		);
		$this->assertSniffError(
			$report,
			130,
			ReturnTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_TYPE_HINT
		);
	}

	public function testIncorrectSpacingInInterface(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/returnTypeHintsIncorrect.php');
		$this->assertSniffError(
			$report,
			46,
			ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE_HINT
		);
		$this->assertSniffError(
			$report,
			48,
			ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE_HINT
		);
		$this->assertSniffError(
			$report,
			50,
			ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE_HINT
		);
		$this->assertSniffError(
			$report,
			52,
			ReturnTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_TYPE_HINT
		);
		$this->assertSniffError(
			$report,
			54,
			ReturnTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_TYPE_HINT
		);
		$this->assertSniffError(
			$report,
			56,
			ReturnTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_TYPE_HINT
		);
		$this->assertSniffError(
			$report,
			58,
			ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON
		);
		$this->assertSniffError(
			$report,
			58,
			ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE_HINT
		);
		$this->assertSniffError(
			$report,
			60,
			ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON
		);
		$this->assertSniffError(
			$report,
			60,
			ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE_HINT
		);
		$this->assertSniffError(
			$report,
			62,
			ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON
		);
		$this->assertSniffError(
			$report,
			62,
			ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE_HINT
		);
		$this->assertSniffError(
			$report,
			64,
			ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON
		);
		$this->assertSniffError(
			$report,
			64,
			ReturnTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_TYPE_HINT
		);
		$this->assertSniffError(
			$report,
			66,
			ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON
		);
		$this->assertSniffError(
			$report,
			66,
			ReturnTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_TYPE_HINT
		);
		$this->assertSniffError(
			$report,
			68,
			ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON
		);
		$this->assertSniffError(
			$report,
			68,
			ReturnTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_TYPE_HINT
		);
	}

	public function testCorrectSpacingWithNullable(): void
	{
		$this->assertNoSniffErrorInFile($this->checkFile(__DIR__ . '/data/returnNullableTypeHintsCorrect.php'));
	}

	public function testIncorrectSpacingWithNullable(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/returnNullableTypeHintsIncorrect.php');

		$this->assertSame(53, $report->getErrorCount());

		$this->assertSniffError($report, 3, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 8, ReturnTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 13, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		$this->assertSniffError($report, 13, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 18, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		$this->assertSniffError($report, 18, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 18, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 23, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 28, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 33, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		$this->assertSniffError($report, 33, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 38, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		$this->assertSniffError($report, 38, ReturnTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 38, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL);

		$this->assertSniffError($report, 46, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 48, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 50, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 52, ReturnTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 54, ReturnTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 56, ReturnTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 58, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		$this->assertSniffError($report, 58, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 60, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		$this->assertSniffError($report, 60, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 62, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		$this->assertSniffError($report, 62, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 64, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		$this->assertSniffError($report, 64, ReturnTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 66, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		$this->assertSniffError($report, 66, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 68, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		$this->assertSniffError($report, 68, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL);

		$this->assertSniffError($report, 75, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 80, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 85, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 90, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 95, ReturnTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 100, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 105, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		$this->assertSniffError($report, 105, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 110, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		$this->assertSniffError($report, 110, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 115, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		$this->assertSniffError($report, 115, ReturnTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 115, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 120, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		$this->assertSniffError($report, 120, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 125, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		$this->assertSniffError($report, 125, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 125, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 130, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		$this->assertSniffError($report, 130, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 130, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL);
	}

	public function testReturnTypeHintsArrayIncorrect(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/returnTypeHintsArrayIncorrect.php');

		$this->assertSame(1, $report->getErrorCount());

		$this->assertSniffError($report, 6, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
	}

	public function testCorrectSpacingWithSpaceBeforeColon(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/returnTypeHintsCorrectSpaceBeforeColon.php', [
			'spacesCountBeforeColon' => 1,
		]);
		$this->assertNoSniffErrorInFile($report);
	}

	public function testIncorrectSpacingWithSpaceBeforeColon(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/returnTypeHintsIncorrectSpaceBeforeColon.php', [
			'spacesCountBeforeColon' => 1,
		]);

		$this->assertSame(24, $report->getErrorCount());

		$message = 'There must be exactly 1 whitespace between closing parenthesis and return type colon.';

		$this->assertSniffError($report, 3, ReturnTypeHintSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON, $message);
		$this->assertSniffError($report, 8, ReturnTypeHintSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON, $message);
		$this->assertSniffError($report, 13, ReturnTypeHintSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON, $message);
		$this->assertSniffError($report, 18, ReturnTypeHintSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON, $message);
		$this->assertSniffError($report, 23, ReturnTypeHintSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON, $message);
		$this->assertSniffError($report, 28, ReturnTypeHintSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON, $message);
		$this->assertSniffError($report, 36, ReturnTypeHintSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON, $message);
		$this->assertSniffError($report, 38, ReturnTypeHintSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON, $message);
		$this->assertSniffError($report, 40, ReturnTypeHintSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON, $message);
		$this->assertSniffError($report, 42, ReturnTypeHintSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON, $message);
		$this->assertSniffError($report, 44, ReturnTypeHintSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON, $message);
		$this->assertSniffError($report, 46, ReturnTypeHintSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON, $message);
		$this->assertSniffError($report, 48, ReturnTypeHintSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON, $message);
		$this->assertSniffError($report, 50, ReturnTypeHintSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON, $message);
		$this->assertSniffError($report, 52, ReturnTypeHintSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON, $message);
		$this->assertSniffError($report, 54, ReturnTypeHintSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON, $message);
		$this->assertSniffError($report, 56, ReturnTypeHintSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON, $message);
		$this->assertSniffError($report, 58, ReturnTypeHintSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON, $message);
		$this->assertSniffError($report, 65, ReturnTypeHintSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON, $message);
		$this->assertSniffError($report, 70, ReturnTypeHintSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON, $message);
		$this->assertSniffError($report, 75, ReturnTypeHintSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON, $message);
		$this->assertSniffError($report, 80, ReturnTypeHintSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON, $message);
		$this->assertSniffError($report, 85, ReturnTypeHintSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON, $message);
		$this->assertSniffError($report, 90, ReturnTypeHintSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON, $message);
	}

	public function testFixableReturnTypeHintNoSpaceBetweenColonAndType(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/fixableReturnTypeHintNoSpaceBetweenColonAndType.php', [], [ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE_HINT]);
		$this->assertAllFixedInFile($report);
	}

	public function testFixableReturnTypeHintMultipleSpacesBetweenColonAndType(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/fixableReturnTypeHintMultipleSpacesBetweenColonAndType.php', [], [ReturnTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_TYPE_HINT]);
		$this->assertAllFixedInFile($report);
	}

	public function testFixableReturnTypeHintNoSpaceBetweenColonAndNullabilitySymbol(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/fixableReturnTypeHintNoSpaceBetweenColonAndNullabilitySymbol.php', [], [ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL]);
		$this->assertAllFixedInFile($report);
	}

	public function testFixableReturnTypeHintMultipleSpacesBetweenColonAndNullabilitySymbol(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/fixableReturnTypeHintMultipleSpacesBetweenColonAndNullabilitySymbol.php', [], [ReturnTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_NULLABILITY_SYMBOL]);
		$this->assertAllFixedInFile($report);
	}

	public function testFixableReturnTypeHintWhitespaceAfterNullabilitySymbol(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/fixableReturnTypeHintWhitespaceAfterNullabilitySymbol.php', [], [ReturnTypeHintSpacingSniff::CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL]);
		$this->assertAllFixedInFile($report);
	}

	public function testFixableReturnTypeHintWhitespaceBeforeColon(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/fixableReturnTypeHintWhitespaceBeforeColon.php', [], [ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON]);
		$this->assertAllFixedInFile($report);
	}

	public function testFixableReturnTypeHintWhitespaceBeforeColonWithSpace(): void
	{
		$report = $this->checkFile(
			__DIR__ . '/data/fixableReturnTypeHintWhitespaceBeforeColonWithSpace.php',
			['spacesCountBeforeColon' => 1],
			[ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON, ReturnTypeHintSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON]
		);
		$this->assertAllFixedInFile($report);
	}

}
