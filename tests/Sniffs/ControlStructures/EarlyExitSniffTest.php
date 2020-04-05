<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use SlevomatCodingStandard\Sniffs\TestCase;

class EarlyExitSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/earlyExitNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/earlyExitErrors.php');

		self::assertSame(64, $report->getErrorCount());

		foreach ([6, 15, 24, 33, 42, 50, 58, 66, 74, 82, 90, 98, 108, 149, 157, 165, 191, 199, 207, 376] as $line) {
			self::assertSniffError($report, $line, EarlyExitSniff::CODE_EARLY_EXIT_NOT_USED, 'Use early exit instead of "else".');
		}

		foreach ([115, 122, 129, 135, 141, 213, 222, 229, 235, 241, 247, 256, 262, 271, 287, 305, 361, 368, 388, 415, 425, 450] as $line) {
			self::assertSniffError($report, $line, EarlyExitSniff::CODE_EARLY_EXIT_NOT_USED, 'Use early exit to reduce code nesting.');
		}

		foreach ([173, 182, 328, 353, 398, 440, 462, 475, 503] as $line) {
			self::assertSniffError($report, $line, EarlyExitSniff::CODE_USELESS_ELSE, 'Remove useless "else" to reduce code nesting.');
		}

		foreach ([322, 324, 326, 336, 338, 340, 351, 396, 406, 436, 460, 473, 492] as $line) {
			self::assertSniffError($report, $line, EarlyExitSniff::CODE_USELESS_ELSEIF, 'Use "if" instead of "elseif".');
		}

		self::assertAllFixedInFile($report);
	}

	public function testIfWithoutCurlyBraces(): void
	{
		$report = self::checkFile(__DIR__ . '/data/earlyExitIfWithoutCurlyBraces.php', [], [EarlyExitSniff::CODE_EARLY_EXIT_NOT_USED]);

		self::assertSame(0, $report->getErrorCount());
	}

	public function testElseifWithoutCurlyBraces(): void
	{
		$report = self::checkFile(__DIR__ . '/data/earlyExitElseifWithoutCurlyBraces.php', [], [EarlyExitSniff::CODE_USELESS_ELSEIF]);

		self::assertSame(0, $report->getErrorCount());
	}

	public function testElseifWithSpace(): void
	{
		$report = self::checkFile(__DIR__ . '/data/earlyExitElseifWithSpace.php', [], [EarlyExitSniff::CODE_USELESS_ELSEIF]);

		self::assertSame(0, $report->getErrorCount());
	}

	public function testElseWithoutCurlyBraces(): void
	{
		$report = self::checkFile(__DIR__ . '/data/earlyExitElseWithoutCurlyBraces.php', [], [EarlyExitSniff::CODE_USELESS_ELSE]);

		self::assertSame(0, $report->getErrorCount());
	}

	public function testInvalidElseif(): void
	{
		$report = self::checkFile(__DIR__ . '/data/earlyExitInvalidElseif.php', [], [EarlyExitSniff::CODE_USELESS_ELSEIF]);

		self::assertSame(0, $report->getErrorCount());
	}

	public function testAlternativeSyntax(): void
	{
		$report = self::checkFile(__DIR__ . '/data/earlyExitAlternativeSyntax.php', [], [EarlyExitSniff::CODE_USELESS_ELSEIF]);

		self::assertSame(0, $report->getErrorCount());
	}

	public function testIgnoredStandaloneIfInScopeNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/earlyExitIgnoredStandaloneIfInScope.php', [
			'ignoreStandaloneIfInScope' => true,
		]);

		self::assertNoSniffErrorInFile($report);
	}

	public function testNotIgnoredStandaloneIfInScopeErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/earlyExitIgnoredStandaloneIfInScope.php', [
			'ignoreStandaloneIfInScope' => false,
		]);

		self::assertSame(2, $report->getErrorCount());

		self::assertSniffError($report, 4, EarlyExitSniff::CODE_EARLY_EXIT_NOT_USED, 'Use early exit to reduce code nesting.');
		self::assertSniffError($report, 11, EarlyExitSniff::CODE_EARLY_EXIT_NOT_USED, 'Use early exit to reduce code nesting.');
	}

	public function testIgnoredOneLineTrailingIfNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/earlyExitIgnoredOneLineTrailingIfNoErrors.php', [
			'ignoreOneLineTrailingIf' => true,
		]);

		self::assertNoSniffErrorInFile($report);
	}

	public function testIgnoredOneLineTrailingIfErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/earlyExitIgnoredOneLineTrailingIfErrors.php', [
			'ignoreOneLineTrailingIf' => true,
		]);

		self::assertSame(2, $report->getErrorCount());

		self::assertSniffError($report, 7, EarlyExitSniff::CODE_USELESS_ELSEIF);
		self::assertSniffError($report, 17, EarlyExitSniff::CODE_USELESS_ELSEIF);

		self::assertAllFixedInFile($report);
	}

}
