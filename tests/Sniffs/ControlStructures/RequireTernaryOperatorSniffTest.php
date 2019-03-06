<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use SlevomatCodingStandard\Sniffs\TestCase;

class RequireTernaryOperatorSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		self::assertNoSniffErrorInFile(self::checkFile(__DIR__ . '/data/requireTernaryOperatorNoErrors.php'));
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireTernaryOperatorErrors.php');

		self::assertSame(4, $report->getErrorCount());

		self::assertSniffError($report, 4, RequireTernaryOperatorSniff::CODE_TERNARY_OPERATOR_NOT_USED);
		self::assertSniffError($report, 11, RequireTernaryOperatorSniff::CODE_TERNARY_OPERATOR_NOT_USED);
		self::assertSniffError($report, 22, RequireTernaryOperatorSniff::CODE_TERNARY_OPERATOR_NOT_USED);
		self::assertSniffError($report, 31, RequireTernaryOperatorSniff::CODE_TERNARY_OPERATOR_NOT_USED);

		self::assertAllFixedInFile($report);
	}

	public function testWithIgnoredMultiLineNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireTernaryOperatorWithIgnoredMultiLineNoErrors.php', [
			'ignoreMultiLine' => true,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testWithIgnoredMultiLineErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireTernaryOperatorWithIgnoredMultiLineErrors.php', [
			'ignoreMultiLine' => true,
		]);

		self::assertSame(2, $report->getErrorCount());

		self::assertSniffError($report, 3, RequireTernaryOperatorSniff::CODE_TERNARY_OPERATOR_NOT_USED);
		self::assertSniffError($report, 16, RequireTernaryOperatorSniff::CODE_TERNARY_OPERATOR_NOT_USED);
	}

	public function testIfWithoutCurlyBraces(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireTernaryOperatorIfWithoutCurlyBraces.php', [], [EarlyExitSniff::CODE_EARLY_EXIT_NOT_USED]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testElseWithoutCurlyBraces(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireTernaryOperatorElseWithoutCurlyBraces.php', [], [EarlyExitSniff::CODE_USELESS_ELSE]);
		self::assertNoSniffErrorInFile($report);
	}

}
