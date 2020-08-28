<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use SlevomatCodingStandard\Sniffs\TestCase;

class ReturnTypeHintSpacingSniffTest extends TestCase
{

	public function testCorrectSpacing(): void
	{
		$report = self::checkFile(__DIR__ . '/data/returnTypeHintsCorrect.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testIncorrectSpacing(): void
	{
		$report = self::checkFile(__DIR__ . '/data/returnTypeHintsIncorrect.php');
		self::assertSniffError($report, 3, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE_HINT);
		self::assertSniffError($report, 8, ReturnTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_TYPE_HINT);
		self::assertSniffError($report, 13, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		self::assertSniffError($report, 13, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE_HINT);
		self::assertSniffError($report, 18, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		self::assertSniffError($report, 23, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE_HINT);
		self::assertSniffError($report, 28, ReturnTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_TYPE_HINT);
		self::assertSniffError($report, 33, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		self::assertSniffError($report, 33, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE_HINT);
		self::assertSniffError($report, 38, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		self::assertSniffError($report, 75, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE_HINT);
		self::assertSniffError($report, 80, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE_HINT);
		self::assertSniffError($report, 85, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE_HINT);
		self::assertSniffError($report, 90, ReturnTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_TYPE_HINT);
		self::assertSniffError($report, 95, ReturnTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_TYPE_HINT);
		self::assertSniffError($report, 100, ReturnTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_TYPE_HINT);
		self::assertSniffError($report, 105, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		self::assertSniffError($report, 105, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE_HINT);
		self::assertSniffError($report, 110, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		self::assertSniffError($report, 110, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE_HINT);
		self::assertSniffError($report, 115, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		self::assertSniffError($report, 115, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE_HINT);
		self::assertSniffError($report, 120, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		self::assertSniffError($report, 120, ReturnTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_TYPE_HINT);
		self::assertSniffError($report, 125, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		self::assertSniffError($report, 125, ReturnTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_TYPE_HINT);
		self::assertSniffError($report, 130, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		self::assertSniffError($report, 130, ReturnTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_TYPE_HINT);

		self::assertSniffError($report, 137, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE_HINT);
		self::assertSniffError($report, 138, ReturnTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_TYPE_HINT);
		self::assertSniffError($report, 139, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
	}

	public function testIncorrectSpacingInInterface(): void
	{
		$report = self::checkFile(__DIR__ . '/data/returnTypeHintsIncorrect.php');
		self::assertSniffError($report, 46, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE_HINT);
		self::assertSniffError($report, 48, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE_HINT);
		self::assertSniffError($report, 50, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE_HINT);
		self::assertSniffError($report, 52, ReturnTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_TYPE_HINT);
		self::assertSniffError($report, 54, ReturnTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_TYPE_HINT);
		self::assertSniffError($report, 56, ReturnTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_TYPE_HINT);
		self::assertSniffError($report, 58, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		self::assertSniffError($report, 58, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE_HINT);
		self::assertSniffError($report, 60, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		self::assertSniffError($report, 60, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE_HINT);
		self::assertSniffError($report, 62, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		self::assertSniffError($report, 62, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE_HINT);
		self::assertSniffError($report, 64, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		self::assertSniffError($report, 64, ReturnTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_TYPE_HINT);
		self::assertSniffError($report, 66, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		self::assertSniffError($report, 66, ReturnTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_TYPE_HINT);
		self::assertSniffError($report, 68, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		self::assertSniffError($report, 68, ReturnTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_TYPE_HINT);
	}

	public function testCorrectSpacingWithNullable(): void
	{
		$report = self::checkFile(__DIR__ . '/data/returnNullableTypeHintsCorrect.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testIncorrectSpacingWithNullable(): void
	{
		$report = self::checkFile(__DIR__ . '/data/returnNullableTypeHintsIncorrect.php');

		self::assertSame(61, $report->getErrorCount());

		self::assertSniffError($report, 3, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		self::assertSniffError($report, 8, ReturnTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		self::assertSniffError($report, 13, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		self::assertSniffError($report, 13, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		self::assertSniffError($report, 18, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		self::assertSniffError($report, 18, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		self::assertSniffError($report, 18, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL);
		self::assertSniffError($report, 23, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		self::assertSniffError($report, 28, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL);
		self::assertSniffError($report, 33, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		self::assertSniffError($report, 33, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		self::assertSniffError($report, 38, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		self::assertSniffError($report, 38, ReturnTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		self::assertSniffError($report, 38, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL);

		self::assertSniffError($report, 46, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		self::assertSniffError($report, 48, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		self::assertSniffError($report, 50, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		self::assertSniffError($report, 52, ReturnTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		self::assertSniffError($report, 54, ReturnTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		self::assertSniffError($report, 56, ReturnTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		self::assertSniffError($report, 58, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		self::assertSniffError($report, 58, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		self::assertSniffError($report, 60, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		self::assertSniffError($report, 60, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		self::assertSniffError($report, 62, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		self::assertSniffError($report, 62, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		self::assertSniffError($report, 64, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		self::assertSniffError($report, 64, ReturnTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		self::assertSniffError($report, 66, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		self::assertSniffError($report, 66, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL);
		self::assertSniffError($report, 68, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		self::assertSniffError($report, 68, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL);

		self::assertSniffError($report, 75, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		self::assertSniffError($report, 80, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		self::assertSniffError($report, 85, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		self::assertSniffError($report, 90, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL);
		self::assertSniffError($report, 95, ReturnTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		self::assertSniffError($report, 100, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL);
		self::assertSniffError($report, 105, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		self::assertSniffError($report, 105, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		self::assertSniffError($report, 110, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		self::assertSniffError($report, 110, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		self::assertSniffError($report, 115, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		self::assertSniffError($report, 115, ReturnTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		self::assertSniffError($report, 115, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL);
		self::assertSniffError($report, 120, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		self::assertSniffError($report, 120, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL);
		self::assertSniffError($report, 125, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		self::assertSniffError($report, 125, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		self::assertSniffError($report, 125, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL);
		self::assertSniffError($report, 130, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		self::assertSniffError($report, 130, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		self::assertSniffError($report, 130, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL);

		self::assertSniffError($report, 137, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		self::assertSniffError($report, 138, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		self::assertSniffError($report, 138, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL);
		self::assertSniffError($report, 139, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		self::assertSniffError($report, 139, ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		self::assertSniffError($report, 140, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		self::assertSniffError($report, 140, ReturnTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		self::assertSniffError($report, 140, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL);
	}

	public function testReturnTypeHintsArrayIncorrect(): void
	{
		$report = self::checkFile(__DIR__ . '/data/returnTypeHintsArrayIncorrect.php');

		self::assertSame(1, $report->getErrorCount());

		self::assertSniffError($report, 6, ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
	}

	public function testCorrectSpacingWithSpaceBeforeColon(): void
	{
		$report = self::checkFile(__DIR__ . '/data/returnTypeHintsCorrectSpaceBeforeColon.php', [
			'spacesCountBeforeColon' => 1,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testIncorrectSpacingWithSpaceBeforeColon(): void
	{
		$report = self::checkFile(__DIR__ . '/data/returnTypeHintsIncorrectSpaceBeforeColon.php', [
			'spacesCountBeforeColon' => 1,
		]);

		self::assertSame(26, $report->getErrorCount());

		$message = 'There must be exactly 1 whitespace between closing parenthesis and return type colon.';

		self::assertSniffError($report, 3, ReturnTypeHintSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON, $message);
		self::assertSniffError($report, 8, ReturnTypeHintSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON, $message);
		self::assertSniffError($report, 13, ReturnTypeHintSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON, $message);
		self::assertSniffError($report, 18, ReturnTypeHintSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON, $message);
		self::assertSniffError($report, 23, ReturnTypeHintSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON, $message);
		self::assertSniffError($report, 28, ReturnTypeHintSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON, $message);
		self::assertSniffError($report, 36, ReturnTypeHintSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON, $message);
		self::assertSniffError($report, 38, ReturnTypeHintSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON, $message);
		self::assertSniffError($report, 40, ReturnTypeHintSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON, $message);
		self::assertSniffError($report, 42, ReturnTypeHintSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON, $message);
		self::assertSniffError($report, 44, ReturnTypeHintSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON, $message);
		self::assertSniffError($report, 46, ReturnTypeHintSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON, $message);
		self::assertSniffError($report, 48, ReturnTypeHintSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON, $message);
		self::assertSniffError($report, 50, ReturnTypeHintSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON, $message);
		self::assertSniffError($report, 52, ReturnTypeHintSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON, $message);
		self::assertSniffError($report, 54, ReturnTypeHintSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON, $message);
		self::assertSniffError($report, 56, ReturnTypeHintSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON, $message);
		self::assertSniffError($report, 58, ReturnTypeHintSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON, $message);
		self::assertSniffError($report, 65, ReturnTypeHintSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON, $message);
		self::assertSniffError($report, 70, ReturnTypeHintSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON, $message);
		self::assertSniffError($report, 75, ReturnTypeHintSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON, $message);
		self::assertSniffError($report, 80, ReturnTypeHintSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON, $message);
		self::assertSniffError($report, 85, ReturnTypeHintSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON, $message);
		self::assertSniffError($report, 90, ReturnTypeHintSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON, $message);
		self::assertSniffError($report, 97, ReturnTypeHintSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON, $message);
		self::assertSniffError($report, 98, ReturnTypeHintSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON, $message);
	}

	public function testFixableReturnTypeHintNoSpaceBetweenColonAndType(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/fixableReturnTypeHintNoSpaceBetweenColonAndType.php',
			[],
			[ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE_HINT]
		);
		self::assertAllFixedInFile($report);
	}

	public function testFixableReturnTypeHintMultipleSpacesBetweenColonAndType(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/fixableReturnTypeHintMultipleSpacesBetweenColonAndType.php',
			[],
			[ReturnTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_TYPE_HINT]
		);
		self::assertAllFixedInFile($report);
	}

	public function testFixableReturnTypeHintNoSpaceBetweenColonAndNullabilitySymbol(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/fixableReturnTypeHintNoSpaceBetweenColonAndNullabilitySymbol.php',
			[],
			[ReturnTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL]
		);
		self::assertAllFixedInFile($report);
	}

	public function testFixableReturnTypeHintMultipleSpacesBetweenColonAndNullabilitySymbol(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/fixableReturnTypeHintMultipleSpacesBetweenColonAndNullabilitySymbol.php',
			[],
			[ReturnTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_NULLABILITY_SYMBOL]
		);
		self::assertAllFixedInFile($report);
	}

	public function testFixableReturnTypeHintWhitespaceAfterNullabilitySymbol(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/fixableReturnTypeHintWhitespaceAfterNullabilitySymbol.php',
			[],
			[ReturnTypeHintSpacingSniff::CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL]
		);
		self::assertAllFixedInFile($report);
	}

	public function testFixableReturnTypeHintWhitespaceBeforeColon(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/fixableReturnTypeHintWhitespaceBeforeColon.php',
			[],
			[ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON]
		);
		self::assertAllFixedInFile($report);
	}

	public function testFixableReturnTypeHintWhitespaceBeforeColonWithSpace(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/fixableReturnTypeHintWhitespaceBeforeColonWithSpace.php',
			['spacesCountBeforeColon' => 1],
			[ReturnTypeHintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON, ReturnTypeHintSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON]
		);
		self::assertAllFixedInFile($report);
	}

}
