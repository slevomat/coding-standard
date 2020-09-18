<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use SlevomatCodingStandard\Sniffs\Namespaces\UndefinedKeywordTokenException;
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
			'Expected 1 lines before "if", found 2.'
		);
		self::assertSniffError(
			$report,
			4,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_CONTROL_STRUCTURE,
			'Expected 1 lines after "if", found 0.'
		);
		self::assertSniffError(
			$report,
			9,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE,
			'Expected 1 lines before "if", found 0.'
		);
		self::assertSniffError(
			$report,
			9,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_CONTROL_STRUCTURE,
			'Expected 1 lines after "if", found 3.'
		);
		self::assertSniffError(
			$report,
			15,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE,
			'Expected 1 lines before "if", found 3.'
		);
		self::assertSniffError(
			$report,
			15,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_CONTROL_STRUCTURE,
			'Expected 1 lines after "if", found 0.'
		);
		self::assertSniffError(
			$report,
			20,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE,
			'Expected 1 lines before "while", found 0.'
		);
		self::assertSniffError(
			$report,
			20,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_CONTROL_STRUCTURE,
			'Expected 1 lines after "while", found 0.'
		);
		self::assertSniffError(
			$report,
			23,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE,
			'Expected 1 lines before "do", found 0.'
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
			'Expected 1 lines after "if", found 0.'
		);
		self::assertSniffError(
			$report,
			36,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE,
			'Expected 1 lines before "if", found 0.'
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
			'Expected 1 lines after "if", found 0.'
		);
		self::assertSniffError(
			$report,
			59,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE,
			'Expected 1 lines before "if", found 0.'
		);
		self::assertSniffError(
			$report,
			59,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_CONTROL_STRUCTURE,
			'Expected 1 lines after "if", found 0.'
		);
		self::assertSniffError(
			$report,
			62,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE,
			'Expected 1 lines before "try", found 0.'
		);
		self::assertSniffError(
			$report,
			72,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE,
			'Expected 1 lines before "if", found 0.'
		);
		self::assertSniffError(
			$report,
			81,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE,
			'Expected 1 lines before "if", found 2.'
		);
		self::assertSniffError(
			$report,
			87,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_CONTROL_STRUCTURE,
			'Expected 1 lines after "if", found 0.'
		);
		self::assertSniffError(
			$report,
			94,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_CONTROL_STRUCTURE,
			'Expected 1 lines after "if", found 2.'
		);
		self::assertSniffError(
			$report,
			106,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE,
			'Expected 1 lines before "foreach", found 0.'
		);

		self::assertAllFixedInFile($report);
	}

	public function testModifiedSettingsNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/blockControlStructureSpacingWithModifiedSettingsNoErrors.php', [
			'linesCountBeforeControlStructure' => 0,
			'linesCountBeforeFirstControlStructure' => 1,
			'linesCountAfterControlStructure' => 0,
			'linesCountAfterLastControlStructure' => 3,
			'tokensToCheck' => [
				'T_DO',
				'T_WHILE',
				'T_FOR',
				'T_FOREACH',
				'T_SWITCH',
				'T_TRY',
			],
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testModifiedSettingsErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/blockControlStructureSpacingWithModifiedSettingsErrors.php', [
			'linesCountBeforeControlStructure' => 0,
			'linesCountBeforeFirstControlStructure' => 1,
			'linesCountAfterControlStructure' => 0,
			'linesCountAfterLastControlStructure' => 3,
			'tokensToCheck' => [
				'T_DO',
				'T_WHILE',
				'T_FOR',
				'T_FOREACH',
				'T_SWITCH',
				'T_TRY',
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
			'Expected 1 lines before "for", found 0.'
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
			'Expected 1 lines before "foreach", found 0.'
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
			'tokensToCheck' => [
				'T_CASE',
				'T_DEFAULT',
			],
		]);
		self::assertSame(6, $report->getErrorCount());

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
			'Expected 1 lines after "case", found 2.'
		);
		self::assertSniffError(
			$report,
			11,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE,
			'Expected 1 lines before "case", found 2.'
		);
		self::assertSniffError(
			$report,
			11,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_CONTROL_STRUCTURE,
			'Expected 1 lines after "case", found 0.'
		);
		self::assertSniffError(
			$report,
			14,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE,
			'Expected 1 lines before "default", found 0.'
		);
		self::assertSniffError(
			$report,
			14,
			BlockControlStructureSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTROL_STRUCTURE,
			'Expected 0 lines after "default", found 1.'
		);

		self::assertAllFixedInFile($report);
	}

	public function testThrowExceptionForUndefinedConstant(): void
	{
		try {
			self::checkFile(
				__DIR__ . '/data/blockControlStructureSpacingWithDefaultSettingsNoErrors.php',
				['tokensToCheck' => ['T_FOO']]
			);
			self::fail();
		} catch (UndefinedKeywordTokenException $e) {
			self::assertStringContainsString('T_FOO', $e->getMessage());
			self::assertSame('T_FOO', $e->getKeyword());
		}
	}

	public function testThrowExceptionForUnsupportedToken(): void
	{
		try {
			self::checkFile(
				__DIR__ . '/data/blockControlStructureSpacingWithDefaultSettingsNoErrors.php',
				['tokensToCheck' => ['T_RETURN']]
			);
			self::fail();
		} catch (UnsupportedTokenException $e) {
			self::assertStringContainsString('T_RETURN', $e->getMessage());
		}
	}

	public function testIfWithoutCurlyBraces(): void
	{
		$report = self::checkFile(__DIR__ . '/data/blockControlStructureSpacingIfWithoutCurlyBraces.php');

		self::assertSame(0, $report->getErrorCount());
	}

	public function testElseWithoutCurlyBraces(): void
	{
		$report = self::checkFile(__DIR__ . '/data/blockControlStructureSpacingElseWithoutCurlyBraces.php');

		self::assertSame(0, $report->getErrorCount());
	}

	public function testIfWithAlternativeSyntax(): void
	{
		$report = self::checkFile(__DIR__ . '/data/blockControlStructureSpacingIfWithAlternativeSyntax.php');

		self::assertSame(0, $report->getErrorCount());
	}

	public function testAtTheEndOfFile(): void
	{
		$report = self::checkFile(__DIR__ . '/data/blockControlStructureSpacingAtTheEndOfFile.php');
		self::assertNoSniffErrorInFile($report);
	}

}
