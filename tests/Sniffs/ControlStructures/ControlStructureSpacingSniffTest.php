<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use SlevomatCodingStandard\Sniffs\Namespaces\UndefinedKeywordTokenException;
use SlevomatCodingStandard\Sniffs\TestCase;
use Throwable;

class ControlStructureSpacingSniffTest extends TestCase
{

	public function testDefaultSettingsNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/controlStructureSpacingWithDefaultSettingsNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testDefaultSettingsErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/controlStructureSpacingWithDefaultSettingsErrors.php');

		self::assertSame(47, $report->getErrorCount());

		self::assertSniffError($report, 4, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE, 'Expected 1 lines before "if", found 2.');
		self::assertSniffError($report, 4, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_CONTROL_STRUCTURE, 'Expected 1 lines after "if", found 0.');
		self::assertSniffError($report, 9, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE, 'Expected 1 lines before "if", found 0.');
		self::assertSniffError($report, 9, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_CONTROL_STRUCTURE, 'Expected 1 lines after "if", found 3.');
		self::assertSniffError($report, 15, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE, 'Expected 1 lines before "if", found 3.');
		self::assertSniffError($report, 15, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_CONTROL_STRUCTURE, 'Expected 1 lines after "if", found 0.');
		self::assertSniffError($report, 20, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE, 'Expected 1 lines before "while", found 0.');
		self::assertSniffError($report, 20, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_CONTROL_STRUCTURE, 'Expected 1 lines after "while", found 0.');
		self::assertSniffError($report, 23, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE, 'Expected 1 lines before "do", found 0.');
		self::assertSniffError($report, 29, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_CONTROL_STRUCTURE, 'Expected 0 lines before "for", found 1.');
		self::assertSniffError($report, 29, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTROL_STRUCTURE, 'Expected 0 lines after "for", found 1.');
		self::assertSniffError($report, 32, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_CONTROL_STRUCTURE, 'Expected 0 lines before "if", found 2.');
		self::assertSniffError($report, 32, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_CONTROL_STRUCTURE, 'Expected 1 lines after "if", found 0.');
		self::assertSniffError($report, 36, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE, 'Expected 1 lines before "if", found 0.');
		self::assertSniffError($report, 36, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTROL_STRUCTURE, 'Expected 0 lines after "if", found 2.');
		self::assertSniffError($report, 47, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_CONTROL_STRUCTURE, 'Expected 0 lines before "foreach", found 1.');
		self::assertSniffError($report, 53, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_CONTROL_STRUCTURE, 'Expected 0 lines before "if", found 2.');
		self::assertSniffError($report, 53, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_CONTROL_STRUCTURE, 'Expected 1 lines after "if", found 0.');
		self::assertSniffError($report, 59, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE, 'Expected 1 lines before "if", found 0.');
		self::assertSniffError($report, 59, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_CONTROL_STRUCTURE, 'Expected 1 lines after "if", found 0.');
		self::assertSniffError($report, 62, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE, 'Expected 1 lines before "try", found 0.');
		self::assertSniffError($report, 67, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE, 'Expected 1 lines before "break", found 0.');
		self::assertSniffError($report, 67, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTROL_STRUCTURE, 'Expected 0 lines after "break", found 1.');
		self::assertSniffError($report, 72, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_CONTROL_STRUCTURE, 'Expected 0 lines before "continue", found 1.');
		self::assertSniffError($report, 72, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTROL_STRUCTURE, 'Expected 0 lines after "continue", found 2.');
		self::assertSniffError($report, 77, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_CONTROL_STRUCTURE, 'Expected 0 lines before "return", found 1.');
		self::assertSniffError($report, 80, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_CONTROL_STRUCTURE, 'Expected 0 lines before "throw", found 1.');
		self::assertSniffError($report, 80, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTROL_STRUCTURE, 'Expected 0 lines after "throw", found 1.');
		self::assertSniffError($report, 90, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE, 'Expected 1 lines before "goto", found 0.');
		self::assertSniffError($report, 90, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_CONTROL_STRUCTURE, 'Expected 1 lines after "goto", found 2.');
		self::assertSniffError($report, 95, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE, 'Expected 1 lines before "yield from", found 0.');
		self::assertSniffError($report, 95, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTROL_STRUCTURE, 'Expected 0 lines after "yield from", found 1.');
		self::assertSniffError($report, 101, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_CONTROL_STRUCTURE, 'Expected 0 lines before "if", found 1.');
		self::assertSniffError($report, 101, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_CONTROL_STRUCTURE, 'Expected 1 lines after "if", found 0.');
		self::assertSniffError($report, 103, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_CONTROL_STRUCTURE, 'Expected 0 lines before "yield", found 1.');
		self::assertSniffError($report, 103, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_CONTROL_STRUCTURE, 'Expected 1 lines after "yield", found 0.');
		self::assertSniffError($report, 104, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE, 'Expected 1 lines before "return", found 0.');
		self::assertSniffError($report, 104, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTROL_STRUCTURE, 'Expected 0 lines after "return", found 2.');
		self::assertSniffError($report, 108, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE, 'Expected 1 lines before "yield", found 0.');
		self::assertSniffError($report, 108, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTROL_STRUCTURE, 'Expected 0 lines after "yield", found 1.');
		self::assertSniffError($report, 124, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE, 'Expected 1 lines before "if", found 0.');
		self::assertSniffError($report, 133, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE, 'Expected 1 lines before "if", found 2.');
		self::assertSniffError($report, 142, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTROL_STRUCTURE, 'Expected 0 lines after "break", found 1.');
		self::assertSniffError($report, 148, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_CONTROL_STRUCTURE, 'Expected 1 lines after "if", found 0.');
		self::assertSniffError($report, 155, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_CONTROL_STRUCTURE, 'Expected 1 lines after "if", found 2.');
		self::assertSniffError($report, 168, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_CONTROL_STRUCTURE, 'Expected 0 lines before "return", found 1.');

		self::assertAllFixedInFile($report);
	}

	public function testModifiedSettingsNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/controlStructureSpacingWithModifiedSettingsNoErrors.php', [
			'linesCountAroundControlStructure' => 0,
			'linesCountBeforeFirstControlStructure' => 1,
			'linesCountAfterLastControlStructure' => 3,
			'tokensToCheck' => [
				'T_DO',
				'T_WHILE',
				'T_FOR',
				'T_FOREACH',
				'T_SWITCH',
				'T_TRY',
				'T_GOTO',
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
		$report = self::checkFile(__DIR__ . '/data/controlStructureSpacingWithModifiedSettingsErrors.php', [
			'linesCountAroundControlStructure' => 0,
			'linesCountBeforeFirstControlStructure' => 1,
			'linesCountAfterLastControlStructure' => 3,
			'tokensToCheck' => [
				'T_DO',
				'T_WHILE',
				'T_FOR',
				'T_FOREACH',
				'T_SWITCH',
				'T_TRY',
				'T_GOTO',
				'T_BREAK',
				'T_CONTINUE',
				'T_RETURN',
				'T_THROW',
				'T_YIELD',
				'T_YIELD_FROM',
			],
		]);

		self::assertSame(23, $report->getErrorCount());

		self::assertSniffError($report, 21, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE, 'Expected 0 lines before "while", found 2.');
		self::assertSniffError($report, 21, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_CONTROL_STRUCTURE, 'Expected 0 lines after "while", found 1.');
		self::assertSniffError($report, 25, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE, 'Expected 0 lines before "do", found 1.');
		self::assertSniffError($report, 25, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_CONTROL_STRUCTURE, 'Expected 0 lines after "do", found 1.');
		self::assertSniffError($report, 30, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_CONTROL_STRUCTURE, 'Expected 1 lines before "for", found 0.');
		self::assertSniffError($report, 30, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTROL_STRUCTURE, 'Expected 3 lines after "for", found 0.');
		self::assertSniffError($report, 43, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_CONTROL_STRUCTURE, 'Expected 1 lines before "foreach", found 0.');
		self::assertSniffError($report, 43, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTROL_STRUCTURE, 'Expected 3 lines after "foreach", found 0.');
		self::assertSniffError($report, 60, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE, 'Expected 0 lines before "try", found 1.');
		self::assertSniffError($report, 60, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTROL_STRUCTURE, 'Expected 3 lines after "try", found 0.');
		self::assertSniffError($report, 65, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTROL_STRUCTURE, 'Expected 3 lines after "break", found 1.');
		self::assertSniffError($report, 70, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTROL_STRUCTURE, 'Expected 3 lines after "continue", found 0.');
		self::assertSniffError($report, 73, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTROL_STRUCTURE, 'Expected 3 lines after "return", found 1.');
		self::assertSniffError($report, 94, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE, 'Expected 0 lines before "goto", found 2.');
		self::assertSniffError($report, 94, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_CONTROL_STRUCTURE, 'Expected 0 lines after "goto", found 1.');
		self::assertSniffError($report, 98, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTROL_STRUCTURE, 'Expected 3 lines after "yield from", found 1.');
		self::assertSniffError($report, 107, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_CONTROL_STRUCTURE, 'Expected 1 lines before "yield", found 2.');
		self::assertSniffError($report, 107, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_CONTROL_STRUCTURE, 'Expected 0 lines after "yield", found 2.');
		self::assertSniffError($report, 110, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE, 'Expected 0 lines before "return", found 2.');
		self::assertSniffError($report, 110, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTROL_STRUCTURE, 'Expected 3 lines after "return", found 2.');
		self::assertSniffError($report, 114, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTROL_STRUCTURE, 'Expected 3 lines after "yield", found 1.');
		self::assertSniffError($report, 121, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE, 'Expected 0 lines before "foreach", found 1.');
		self::assertSniffError($report, 131, ControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTROL_STRUCTURE, 'Expected 3 lines after "foreach", found 0.');

		self::assertAllFixedInFile($report);
	}

	public function testThrowExceptionForUndefinedConstant(): void
	{
		try {
			self::checkFile(
				__DIR__ . '/data/controlStructureSpacingWithDefaultSettingsNoErrors.php',
				['tokensToCheck' => ['T_FOO']]
			);
			self::fail();
		} catch (UndefinedKeywordTokenException $e) {
			self::assertContains('T_FOO', $e->getMessage());
			self::assertSame('T_FOO', $e->getKeyword());
		}
	}

	public function testIfWithoutCurlyBraces(): void
	{
		self::expectException(Throwable::class);
		self::expectExceptionMessage('"if" without curly braces is not supported.');
		self::checkFile(__DIR__ . '/data/controlStructureSpacingIfWithoutCurlyBraces.php');
	}

	public function testElseWithoutCurlyBraces(): void
	{
		self::expectException(Throwable::class);
		self::expectExceptionMessage('"else" without curly braces is not supported.');
		self::checkFile(__DIR__ . '/data/controlStructureSpacingElseWithoutCurlyBraces.php');
	}

	public function testAtTheEndOfFile(): void
	{
		$report = self::checkFile(__DIR__ . '/data/controlStructureSpacingAtTheEndOfFile.php');
		self::assertNoSniffErrorInFile($report);
	}

}
