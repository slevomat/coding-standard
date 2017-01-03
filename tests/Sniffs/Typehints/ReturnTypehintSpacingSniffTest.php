<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Typehints;

class ReturnTypehintSpacingSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testCorrectSpacing()
	{
		$this->assertNoSniffErrorInFile($this->checkFile(__DIR__ . '/data/returnTypehintsCorrect.php'));
	}

	public function testIncorrectSpacing()
	{
		$report = $this->checkFile(__DIR__ . '/data/returnTypehintsIncorrect.php');
		$this->assertSniffError(
			$report,
			3,
			ReturnTypehintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE
		);
		$this->assertSniffError(
			$report,
			8,
			ReturnTypehintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_TYPE
		);
		$this->assertSniffError(
			$report,
			13,
			ReturnTypehintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON
		);
		$this->assertSniffError(
			$report,
			13,
			ReturnTypehintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE
		);
		$this->assertSniffError(
			$report,
			18,
			ReturnTypehintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON
		);
		$this->assertSniffError(
			$report,
			23,
			ReturnTypehintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE
		);
		$this->assertSniffError(
			$report,
			28,
			ReturnTypehintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_TYPE
		);
		$this->assertSniffError(
			$report,
			33,
			ReturnTypehintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON
		);
		$this->assertSniffError(
			$report,
			33,
			ReturnTypehintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE
		);
		$this->assertSniffError(
			$report,
			38,
			ReturnTypehintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON
		);
		$this->assertSniffError(
			$report,
			75,
			ReturnTypehintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE
		);
		$this->assertSniffError(
			$report,
			80,
			ReturnTypehintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE
		);
		$this->assertSniffError(
			$report,
			85,
			ReturnTypehintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE
		);
		$this->assertSniffError(
			$report,
			90,
			ReturnTypehintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_TYPE
		);
		$this->assertSniffError(
			$report,
			95,
			ReturnTypehintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_TYPE
		);
		$this->assertSniffError(
			$report,
			100,
			ReturnTypehintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_TYPE
		);
		$this->assertSniffError(
			$report,
			105,
			ReturnTypehintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON
		);
		$this->assertSniffError(
			$report,
			105,
			ReturnTypehintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE
		);
		$this->assertSniffError(
			$report,
			110,
			ReturnTypehintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON
		);
		$this->assertSniffError(
			$report,
			110,
			ReturnTypehintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE
		);
		$this->assertSniffError(
			$report,
			115,
			ReturnTypehintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON
		);
		$this->assertSniffError(
			$report,
			115,
			ReturnTypehintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE
		);
		$this->assertSniffError(
			$report,
			120,
			ReturnTypehintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON
		);
		$this->assertSniffError(
			$report,
			120,
			ReturnTypehintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_TYPE
		);
		$this->assertSniffError(
			$report,
			125,
			ReturnTypehintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON
		);
		$this->assertSniffError(
			$report,
			125,
			ReturnTypehintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_TYPE
		);
		$this->assertSniffError(
			$report,
			130,
			ReturnTypehintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON
		);
		$this->assertSniffError(
			$report,
			130,
			ReturnTypehintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_TYPE
		);
	}

	public function testIncorrectSpacingInInterface()
	{
		$report = $this->checkFile(__DIR__ . '/data/returnTypehintsIncorrect.php');
		$this->assertSniffError(
			$report,
			46,
			ReturnTypehintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE
		);
		$this->assertSniffError(
			$report,
			48,
			ReturnTypehintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE
		);
		$this->assertSniffError(
			$report,
			50,
			ReturnTypehintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE
		);
		$this->assertSniffError(
			$report,
			52,
			ReturnTypehintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_TYPE
		);
		$this->assertSniffError(
			$report,
			54,
			ReturnTypehintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_TYPE
		);
		$this->assertSniffError(
			$report,
			56,
			ReturnTypehintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_TYPE
		);
		$this->assertSniffError(
			$report,
			58,
			ReturnTypehintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON
		);
		$this->assertSniffError(
			$report,
			58,
			ReturnTypehintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE
		);
		$this->assertSniffError(
			$report,
			60,
			ReturnTypehintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON
		);
		$this->assertSniffError(
			$report,
			60,
			ReturnTypehintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE
		);
		$this->assertSniffError(
			$report,
			62,
			ReturnTypehintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON
		);
		$this->assertSniffError(
			$report,
			62,
			ReturnTypehintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE
		);
		$this->assertSniffError(
			$report,
			64,
			ReturnTypehintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON
		);
		$this->assertSniffError(
			$report,
			64,
			ReturnTypehintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_TYPE
		);
		$this->assertSniffError(
			$report,
			66,
			ReturnTypehintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON
		);
		$this->assertSniffError(
			$report,
			66,
			ReturnTypehintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_TYPE
		);
		$this->assertSniffError(
			$report,
			68,
			ReturnTypehintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON
		);
		$this->assertSniffError(
			$report,
			68,
			ReturnTypehintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_TYPE
		);
	}

	public function testCorrectSpacingWithNullable()
	{
		$this->assertNoSniffErrorInFile($this->checkFile(__DIR__ . '/data/returnNullableTypehintsCorrect.php'));
	}

	public function testIncorrectSpacingWithNullable()
	{
		$report = $this->checkFile(__DIR__ . '/data/returnNullableTypehintsIncorrect.php');

		$this->assertSame(53, $report->getErrorCount());

		$this->assertSniffError($report, 3, ReturnTypehintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 8, ReturnTypehintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 13, ReturnTypehintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		$this->assertSniffError($report, 13, ReturnTypehintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 18, ReturnTypehintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		$this->assertSniffError($report, 18, ReturnTypehintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 18, ReturnTypehintSpacingSniff::CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 23, ReturnTypehintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 28, ReturnTypehintSpacingSniff::CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 33, ReturnTypehintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		$this->assertSniffError($report, 33, ReturnTypehintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 38, ReturnTypehintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		$this->assertSniffError($report, 38, ReturnTypehintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 38, ReturnTypehintSpacingSniff::CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL);

		$this->assertSniffError($report, 46, ReturnTypehintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 48, ReturnTypehintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 50, ReturnTypehintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 52, ReturnTypehintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 54, ReturnTypehintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 56, ReturnTypehintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 58, ReturnTypehintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		$this->assertSniffError($report, 58, ReturnTypehintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 60, ReturnTypehintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		$this->assertSniffError($report, 60, ReturnTypehintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 62, ReturnTypehintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		$this->assertSniffError($report, 62, ReturnTypehintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 64, ReturnTypehintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		$this->assertSniffError($report, 64, ReturnTypehintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 66, ReturnTypehintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		$this->assertSniffError($report, 66, ReturnTypehintSpacingSniff::CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 68, ReturnTypehintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		$this->assertSniffError($report, 68, ReturnTypehintSpacingSniff::CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL);

		$this->assertSniffError($report, 75, ReturnTypehintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 80, ReturnTypehintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 85, ReturnTypehintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 90, ReturnTypehintSpacingSniff::CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 95, ReturnTypehintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 100, ReturnTypehintSpacingSniff::CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 105, ReturnTypehintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		$this->assertSniffError($report, 105, ReturnTypehintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 110, ReturnTypehintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		$this->assertSniffError($report, 110, ReturnTypehintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 115, ReturnTypehintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		$this->assertSniffError($report, 115, ReturnTypehintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 115, ReturnTypehintSpacingSniff::CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 120, ReturnTypehintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		$this->assertSniffError($report, 120, ReturnTypehintSpacingSniff::CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 125, ReturnTypehintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		$this->assertSniffError($report, 125, ReturnTypehintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 125, ReturnTypehintSpacingSniff::CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 130, ReturnTypehintSpacingSniff::CODE_WHITESPACE_BEFORE_COLON);
		$this->assertSniffError($report, 130, ReturnTypehintSpacingSniff::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
		$this->assertSniffError($report, 130, ReturnTypehintSpacingSniff::CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL);
	}

}
