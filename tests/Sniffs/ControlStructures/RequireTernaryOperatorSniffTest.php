<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use SlevomatCodingStandard\Sniffs\TestCase;
use Throwable;

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

		$this->assertAllFixedInFile($report);
	}

	public function testWithIgnoredMultilineNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireTernaryOperatorWithIgnoredMultilineNoErrors.php', [
			'ignoreMultiline' => true,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testWithIgnoredMultilineErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireTernaryOperatorWithIgnoredMultilineErrors.php', [
			'ignoreMultiline' => true,
		]);

		self::assertSame(2, $report->getErrorCount());

		self::assertSniffError($report, 3, RequireTernaryOperatorSniff::CODE_TERNARY_OPERATOR_NOT_USED);
		self::assertSniffError($report, 16, RequireTernaryOperatorSniff::CODE_TERNARY_OPERATOR_NOT_USED);
	}

	public function testIfWithoutCurlyBraces(): void
	{
		self::expectException(Throwable::class);
		self::expectExceptionMessage('"if" without curly braces is not supported.');
		self::checkFile(__DIR__ . '/data/requireTernaryOperatorIfWithoutCurlyBraces.php', [], [EarlyExitSniff::CODE_EARLY_EXIT_NOT_USED]);
	}

	public function testElseWithoutCurlyBraces(): void
	{
		self::expectException(Throwable::class);
		self::expectExceptionMessage('"else" without curly braces is not supported.');
		self::checkFile(__DIR__ . '/data/requireTernaryOperatorElseWithoutCurlyBraces.php', [], [EarlyExitSniff::CODE_USELESS_ELSE]);
	}

}
