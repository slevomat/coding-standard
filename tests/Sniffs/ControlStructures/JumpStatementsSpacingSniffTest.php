<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use SlevomatCodingStandard\Sniffs\TestCase;

class JumpStatementsSpacingSniffTest extends TestCase
{

	public function testDefaultSettingsNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/jumpStatementsSpacingWithDefaultSettingsNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testDefaultSettingsErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/jumpStatementsSpacingWithDefaultSettingsErrors.php');

		self::assertSame(29, $report->getErrorCount());

		self::assertSniffError(
			$report,
			10,
			JumpStatementsSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE,
			'Expected 1 lines before "break", found 0.'
		);
		self::assertSniffError(
			$report,
			10,
			JumpStatementsSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTROL_STRUCTURE,
			'Expected 0 lines after "break", found 1.'
		);
		self::assertSniffError(
			$report,
			15,
			JumpStatementsSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_CONTROL_STRUCTURE,
			'Expected 0 lines before "continue", found 1.'
		);
		self::assertSniffError(
			$report,
			15,
			JumpStatementsSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTROL_STRUCTURE,
			'Expected 0 lines after "continue", found 2.'
		);
		self::assertSniffError(
			$report,
			20,
			JumpStatementsSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_CONTROL_STRUCTURE,
			'Expected 0 lines before "return", found 1.'
		);
		self::assertSniffError(
			$report,
			23,
			JumpStatementsSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_CONTROL_STRUCTURE,
			'Expected 0 lines before "throw", found 1.'
		);
		self::assertSniffError(
			$report,
			23,
			JumpStatementsSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTROL_STRUCTURE,
			'Expected 0 lines after "throw", found 1.'
		);
		self::assertSniffError(
			$report,
			32,
			JumpStatementsSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE,
			'Expected 1 lines before "goto", found 0.'
		);
		self::assertSniffError(
			$report,
			32,
			JumpStatementsSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_CONTROL_STRUCTURE,
			'Expected 1 lines after "goto", found 2.'
		);
		self::assertSniffError(
			$report,
			37,
			JumpStatementsSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE,
			'Expected 1 lines before "yield from", found 0.'
		);
		self::assertSniffError(
			$report,
			37,
			JumpStatementsSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTROL_STRUCTURE,
			'Expected 0 lines after "yield from", found 1.'
		);
		self::assertSniffError(
			$report,
			44,
			JumpStatementsSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_CONTROL_STRUCTURE,
			'Expected 0 lines before "yield", found 1.'
		);
		self::assertSniffError(
			$report,
			44,
			JumpStatementsSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_CONTROL_STRUCTURE,
			'Expected 1 lines after "yield", found 0.'
		);
		self::assertSniffError(
			$report,
			45,
			JumpStatementsSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE,
			'Expected 1 lines before "return", found 0.'
		);
		self::assertSniffError(
			$report,
			45,
			JumpStatementsSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTROL_STRUCTURE,
			'Expected 0 lines after "return", found 2.'
		);
		self::assertSniffError(
			$report,
			49,
			JumpStatementsSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE,
			'Expected 1 lines before "yield", found 0.'
		);
		self::assertSniffError(
			$report,
			49,
			JumpStatementsSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTROL_STRUCTURE,
			'Expected 0 lines after "yield", found 1.'
		);
		self::assertSniffError(
			$report,
			60,
			JumpStatementsSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE,
			'Expected 1 lines before "return", found 0.'
		);
		self::assertSniffError(
			$report,
			67,
			JumpStatementsSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTROL_STRUCTURE,
			'Expected 0 lines after "break", found 1.'
		);
		self::assertSniffError(
			$report,
			77,
			JumpStatementsSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_CONTROL_STRUCTURE,
			'Expected 0 lines before "return", found 1.'
		);
		self::assertSniffError(
			$report,
			82,
			JumpStatementsSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_CONTROL_STRUCTURE,
			'Expected 0 lines before "return", found 1.'
		);
		self::assertSniffError(
			$report,
			91,
			JumpStatementsSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTROL_STRUCTURE,
			'Expected 0 lines after "return", found 1.'
		);
		self::assertSniffError(
			$report,
			108,
			JumpStatementsSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_CONTROL_STRUCTURE,
			'Expected 0 lines before "return", found 1.'
		);
		self::assertSniffError(
			$report,
			108,
			JumpStatementsSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTROL_STRUCTURE,
			'Expected 0 lines after "return", found 1.'
		);
		self::assertSniffError(
			$report,
			126,
			JumpStatementsSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_CONTROL_STRUCTURE,
			'Expected 1 lines after "yield", found 0.'
		);
		self::assertSniffError(
			$report,
			127,
			JumpStatementsSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE,
			'Expected 1 lines before "return", found 0.'
		);
		self::assertSniffError(
			$report,
			127,
			JumpStatementsSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_CONTROL_STRUCTURE,
			'Expected 1 lines after "return", found 0.'
		);
		self::assertSniffError(
			$report,
			128,
			JumpStatementsSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE,
			'Expected 1 lines before "yield", found 0.'
		);
		self::assertSniffError(
			$report,
			139,
			JumpStatementsSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE,
			'Expected 1 lines before "return", found 2.'
		);

		self::assertAllFixedInFile($report);
	}

	public function testModifiedSettingsNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/jumpStatementsSpacingWithModifiedSettingsNoErrors.php', [
			'linesCountBeforeControlStructure' => 0,
			'linesCountBeforeFirstControlStructure' => 1,
			'linesCountAfterControlStructure' => 0,
			'linesCountAfterLastControlStructure' => 3,
			'tokensToCheck' => [
				'T_BREAK',
				'T_CONTINUE',
				'T_RETURN',
				'T_THROW',
				'T_YIELD',
				'T_YIELD_FROM',
			],
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testModifiedSettingsErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/jumpStatementsSpacingWithModifiedSettingsErrors.php', [
			'linesCountBeforeControlStructure' => 0,
			'linesCountBeforeFirstControlStructure' => 1,
			'linesCountAfterControlStructure' => 0,
			'linesCountAfterLastControlStructure' => 3,
			'tokensToCheck' => [
				'T_BREAK',
				'T_CONTINUE',
				'T_RETURN',
				'T_THROW',
				'T_YIELD',
				'T_YIELD_FROM',
			],
		]);
		self::assertSame(9, $report->getErrorCount());

		self::assertSniffError(
			$report,
			9,
			JumpStatementsSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTROL_STRUCTURE,
			'Expected 3 lines after "break", found 1.'
		);
		self::assertSniffError(
			$report,
			14,
			JumpStatementsSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTROL_STRUCTURE,
			'Expected 3 lines after "continue", found 0.'
		);
		self::assertSniffError(
			$report,
			17,
			JumpStatementsSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTROL_STRUCTURE,
			'Expected 3 lines after "return", found 1.'
		);
		self::assertSniffError(
			$report,
			40,
			JumpStatementsSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTROL_STRUCTURE,
			'Expected 3 lines after "yield from", found 1.'
		);
		self::assertSniffError(
			$report,
			48,
			JumpStatementsSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_CONTROL_STRUCTURE,
			'Expected 1 lines before "yield", found 2.'
		);
		self::assertSniffError(
			$report,
			48,
			JumpStatementsSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_CONTROL_STRUCTURE,
			'Expected 0 lines after "yield", found 2.'
		);
		self::assertSniffError(
			$report,
			51,
			JumpStatementsSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE,
			'Expected 0 lines before "return", found 2.'
		);
		self::assertSniffError(
			$report,
			51,
			JumpStatementsSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTROL_STRUCTURE,
			'Expected 3 lines after "return", found 2.'
		);
		self::assertSniffError(
			$report,
			55,
			JumpStatementsSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTROL_STRUCTURE,
			'Expected 3 lines after "yield", found 1.'
		);

		self::assertAllFixedInFile($report);
	}

	public function testAtTheEndOfFile(): void
	{
		$report = self::checkFile(__DIR__ . '/data/jumpStatementsSpacingAtTheEndOfFile.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testIgnoreWhenInCaseOrDefaultStatementNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/jumpStatementsWhenInCaseOrDefaultNoErrors.php', [
			'linesCountBeforeWhenFirstInCaseOrDefault' => 0,
			'linesCountAfterWhenLastInCaseOrDefault' => 1,
			'linesCountAfterWhenLastInLastCaseOrDefault' => 0,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testWhenInCaseOrDefaultStatementErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/jumpStatementsWhenInCaseOrDefaultErrors.php', [
			'linesCountBeforeWhenFirstInCaseOrDefault' => 0,
			'linesCountAfterWhenLastInCaseOrDefault' => 1,
			'linesCountAfterWhenLastInLastCaseOrDefault' => 0,
		]);

		self::assertSame(4, $report->getErrorCount());

		self::assertSniffError(
			$report,
			7,
			JumpStatementsSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_CONTROL_STRUCTURE,
			'Expected 0 lines before "throw", found 1.'
		);
		self::assertSniffError(
			$report,
			7,
			JumpStatementsSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTROL_STRUCTURE,
			'Expected 1 lines after "throw", found 0.'
		);
		self::assertSniffError(
			$report,
			17,
			JumpStatementsSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTROL_STRUCTURE,
			'Expected 1 lines after "break", found 0.'
		);
		self::assertSniffError(
			$report,
			22,
			JumpStatementsSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTROL_STRUCTURE,
			'Expected 0 lines after "break", found 1.'
		);

		self::assertAllFixedInFile($report);
	}

}
