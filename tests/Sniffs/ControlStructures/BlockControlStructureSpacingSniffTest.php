<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use SlevomatCodingStandard\Sniffs\TestCase;

class BlockControlStructureSpacingSniffTest extends TestCase
{

	public function testDefaultSettingsNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/blockControlStructureSpacingWithDefaultSettingsNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testDefaultSettingsErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/blockControlStructureSpacingWithDefaultSettingsErrors.php');

		self::assertSame(26, $report->getErrorCount());

		self::assertSniffError(
			$report,
			4,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE,
			'Expected 1 line before "if", found 2.'
		);
		self::assertSniffError(
			$report,
			4,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_CONTROL_STRUCTURE,
			'Expected 1 line after "if", found 0.'
		);
		self::assertSniffError(
			$report,
			9,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE,
			'Expected 1 line before "if", found 0.'
		);
		self::assertSniffError(
			$report,
			9,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_CONTROL_STRUCTURE,
			'Expected 1 line after "if", found 3.'
		);
		self::assertSniffError(
			$report,
			15,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE,
			'Expected 1 line before "if", found 3.'
		);
		self::assertSniffError(
			$report,
			15,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_CONTROL_STRUCTURE,
			'Expected 1 line after "if", found 0.'
		);
		self::assertSniffError(
			$report,
			20,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE,
			'Expected 1 line before "while", found 0.'
		);
		self::assertSniffError(
			$report,
			20,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_CONTROL_STRUCTURE,
			'Expected 1 line after "while", found 0.'
		);
		self::assertSniffError(
			$report,
			23,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE,
			'Expected 1 line before "do", found 0.'
		);
		self::assertSniffError(
			$report,
			29,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_CONTROL_STRUCTURE,
			'Expected 0 lines before "for", found 1.'
		);
		self::assertSniffError(
			$report,
			29,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTROL_STRUCTURE,
			'Expected 0 lines after "for", found 1.'
		);
		self::assertSniffError(
			$report,
			32,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_CONTROL_STRUCTURE,
			'Expected 0 lines before "if", found 2.'
		);
		self::assertSniffError(
			$report,
			32,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_CONTROL_STRUCTURE,
			'Expected 1 line after "if", found 0.'
		);
		self::assertSniffError(
			$report,
			36,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE,
			'Expected 1 line before "if", found 0.'
		);
		self::assertSniffError(
			$report,
			36,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTROL_STRUCTURE,
			'Expected 0 lines after "if", found 2.'
		);
		self::assertSniffError(
			$report,
			47,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_CONTROL_STRUCTURE,
			'Expected 0 lines before "foreach", found 1.'
		);
		self::assertSniffError(
			$report,
			53,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_CONTROL_STRUCTURE,
			'Expected 0 lines before "if", found 2.'
		);
		self::assertSniffError(
			$report,
			53,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_CONTROL_STRUCTURE,
			'Expected 1 line after "if", found 0.'
		);
		self::assertSniffError(
			$report,
			59,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE,
			'Expected 1 line before "if", found 0.'
		);
		self::assertSniffError(
			$report,
			59,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_CONTROL_STRUCTURE,
			'Expected 1 line after "if", found 0.'
		);
		self::assertSniffError(
			$report,
			62,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE,
			'Expected 1 line before "try", found 0.'
		);
		self::assertSniffError(
			$report,
			72,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE,
			'Expected 1 line before "if", found 0.'
		);
		self::assertSniffError(
			$report,
			81,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE,
			'Expected 1 line before "if", found 2.'
		);
		self::assertSniffError(
			$report,
			87,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_CONTROL_STRUCTURE,
			'Expected 1 line after "if", found 0.'
		);
		self::assertSniffError(
			$report,
			94,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_CONTROL_STRUCTURE,
			'Expected 1 line after "if", found 2.'
		);
		self::assertSniffError(
			$report,
			106,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE,
			'Expected 1 line before "foreach", found 0.'
		);

		self::assertAllFixedInFile($report);
	}

	public function testModifiedSettingsNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/blockControlStructureSpacingWithModifiedSettingsNoErrors.php', [
			'linesCountBefore' => 0,
			'linesCountBeforeFirst' => 1,
			'linesCountAfter' => 0,
			'linesCountAfterLast' => 3,
			'controlStructures' => [
				'do',
				'while',
				'for',
				'foreach',
				'switch',
				'try',
			],
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testModifiedSettingsErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/blockControlStructureSpacingWithModifiedSettingsErrors.php', [
			'linesCountBefore' => 0,
			'linesCountBeforeFirst' => 1,
			'linesCountAfter' => 0,
			'linesCountAfterLast' => 3,
			'controlStructures' => [
				'do',
				'while',
				'for',
				'foreach',
				'switch',
				'try',
			],
		]);
		self::assertSame(12, $report->getErrorCount());

		self::assertSniffError(
			$report,
			21,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE,
			'Expected 0 lines before "while", found 2.'
		);
		self::assertSniffError(
			$report,
			21,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_CONTROL_STRUCTURE,
			'Expected 0 lines after "while", found 1.'
		);
		self::assertSniffError(
			$report,
			25,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE,
			'Expected 0 lines before "do", found 1.'
		);
		self::assertSniffError(
			$report,
			25,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_CONTROL_STRUCTURE,
			'Expected 0 lines after "do", found 1.'
		);
		self::assertSniffError(
			$report,
			30,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_CONTROL_STRUCTURE,
			'Expected 1 line before "for", found 0.'
		);
		self::assertSniffError(
			$report,
			30,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTROL_STRUCTURE,
			'Expected 3 lines after "for", found 0.'
		);
		self::assertSniffError(
			$report,
			43,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_CONTROL_STRUCTURE,
			'Expected 1 line before "foreach", found 0.'
		);
		self::assertSniffError(
			$report,
			43,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTROL_STRUCTURE,
			'Expected 3 lines after "foreach", found 0.'
		);
		self::assertSniffError(
			$report,
			60,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE,
			'Expected 0 lines before "try", found 1.'
		);
		self::assertSniffError(
			$report,
			60,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTROL_STRUCTURE,
			'Expected 3 lines after "try", found 0.'
		);
		self::assertSniffError(
			$report,
			76,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE,
			'Expected 0 lines before "foreach", found 1.'
		);
		self::assertSniffError(
			$report,
			86,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTROL_STRUCTURE,
			'Expected 3 lines after "foreach", found 0.'
		);

		self::assertAllFixedInFile($report);
	}

	public function testSwitchErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/blockControlStructureSpacingSwitchErrors.php', [
			'controlStructures' => [
				'case',
				'default',
			],
		]);

		self::assertSame(8, $report->getErrorCount());

		self::assertSniffError(
			$report,
			6,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_CONTROL_STRUCTURE,
			'Expected 0 lines before "case", found 1.'
		);
		self::assertSniffError(
			$report,
			6,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_CONTROL_STRUCTURE,
			'Expected 1 line after "case", found 2.'
		);
		self::assertSniffError(
			$report,
			11,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE,
			'Expected 1 line before "case", found 2.'
		);
		self::assertSniffError(
			$report,
			11,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_CONTROL_STRUCTURE,
			'Expected 1 line after "case", found 0.'
		);
		self::assertSniffError(
			$report,
			14,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE,
			'Expected 1 line before "default", found 0.'
		);
		self::assertSniffError(
			$report,
			14,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTROL_STRUCTURE,
			'Expected 0 lines after "default", found 1.'
		);

		self::assertNoSniffError($report, 21);
		self::assertNoSniffError($report, 22);
		self::assertNoSniffError($report, 27);
		self::assertNoSniffError($report, 28);

		self::assertSniffError(
			$report,
			33,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_CONTROL_STRUCTURE,
			'Expected 1 line after "case", found 2.'
		);
		self::assertSniffError(
			$report,
			38,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE,
			'Expected 1 line before "case", found 2.'
		);

		self::assertAllFixedInFile($report);
	}

	public function testThrowExceptionForUnsupportedKeyword(): void
	{
		try {
			self::checkFile(
				__DIR__ . '/data/blockControlStructureSpacingWithDefaultSettingsNoErrors.php',
				['controlStructures' => ['whatever']]
			);
			self::fail();
		} catch (UnsupportedKeywordException $e) {
			self::assertSame('"whatever" is not supported.', $e->getMessage());
		}
	}

	public function testIfWithoutCurlyBraces(): void
	{
		$report = self::checkFile(__DIR__ . '/data/blockControlStructureSpacingIfWithoutCurlyBraces.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testElseWithoutCurlyBraces(): void
	{
		$report = self::checkFile(__DIR__ . '/data/blockControlStructureSpacingElseWithoutCurlyBraces.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testIfWithAlternativeSyntax(): void
	{
		$report = self::checkFile(__DIR__ . '/data/blockControlStructureSpacingIfWithAlternativeSyntax.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testAtTheEndOfFile(): void
	{
		$report = self::checkFile(__DIR__ . '/data/blockControlStructureSpacingAtTheEndOfFile.php');
		self::assertNoSniffErrorInFile($report);
	}

}
