<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use SlevomatCodingStandard\Sniffs\TestCase;

class RequireShortTernaryOperatorSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireShortTernaryOperatorNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireShortTernaryOperatorErrors.php');

		self::assertSame(29, $report->getErrorCount());

		self::assertSniffError($report, 6, RequireShortTernaryOperatorSniff::CODE_REQUIRED_SHORT_TERNARY_OPERATOR);
		self::assertSniffError($report, 8, RequireShortTernaryOperatorSniff::CODE_REQUIRED_SHORT_TERNARY_OPERATOR);
		self::assertSniffError($report, 10, RequireShortTernaryOperatorSniff::CODE_REQUIRED_SHORT_TERNARY_OPERATOR);
		self::assertSniffError($report, 13, RequireShortTernaryOperatorSniff::CODE_REQUIRED_SHORT_TERNARY_OPERATOR);
		self::assertSniffError($report, 17, RequireShortTernaryOperatorSniff::CODE_REQUIRED_SHORT_TERNARY_OPERATOR);
		self::assertSniffError($report, 18, RequireShortTernaryOperatorSniff::CODE_REQUIRED_SHORT_TERNARY_OPERATOR);
		self::assertSniffError($report, 21, RequireShortTernaryOperatorSniff::CODE_REQUIRED_SHORT_TERNARY_OPERATOR);
		self::assertSniffError($report, 23, RequireShortTernaryOperatorSniff::CODE_REQUIRED_SHORT_TERNARY_OPERATOR);
		self::assertSniffError($report, 26, RequireShortTernaryOperatorSniff::CODE_REQUIRED_SHORT_TERNARY_OPERATOR);
		self::assertSniffError($report, 33, RequireShortTernaryOperatorSniff::CODE_REQUIRED_SHORT_TERNARY_OPERATOR);
		self::assertSniffError($report, 34, RequireShortTernaryOperatorSniff::CODE_REQUIRED_SHORT_TERNARY_OPERATOR);
		self::assertSniffError($report, 35, RequireShortTernaryOperatorSniff::CODE_REQUIRED_SHORT_TERNARY_OPERATOR);
		self::assertSniffError($report, 36, RequireShortTernaryOperatorSniff::CODE_REQUIRED_SHORT_TERNARY_OPERATOR);
		self::assertSniffError($report, 37, RequireShortTernaryOperatorSniff::CODE_REQUIRED_SHORT_TERNARY_OPERATOR);
		self::assertSniffError($report, 38, RequireShortTernaryOperatorSniff::CODE_REQUIRED_SHORT_TERNARY_OPERATOR);
		self::assertSniffError($report, 39, RequireShortTernaryOperatorSniff::CODE_REQUIRED_SHORT_TERNARY_OPERATOR);
		self::assertSniffError($report, 40, RequireShortTernaryOperatorSniff::CODE_REQUIRED_SHORT_TERNARY_OPERATOR);
		self::assertSniffError($report, 41, RequireShortTernaryOperatorSniff::CODE_REQUIRED_SHORT_TERNARY_OPERATOR);
		self::assertSniffError($report, 42, RequireShortTernaryOperatorSniff::CODE_REQUIRED_SHORT_TERNARY_OPERATOR);
		self::assertSniffError($report, 43, RequireShortTernaryOperatorSniff::CODE_REQUIRED_SHORT_TERNARY_OPERATOR);
		self::assertSniffError($report, 44, RequireShortTernaryOperatorSniff::CODE_REQUIRED_SHORT_TERNARY_OPERATOR);
		self::assertSniffError($report, 45, RequireShortTernaryOperatorSniff::CODE_REQUIRED_SHORT_TERNARY_OPERATOR);
		self::assertSniffError($report, 46, RequireShortTernaryOperatorSniff::CODE_REQUIRED_SHORT_TERNARY_OPERATOR);
		self::assertSniffError($report, 47, RequireShortTernaryOperatorSniff::CODE_REQUIRED_SHORT_TERNARY_OPERATOR);
		self::assertSniffError($report, 48, RequireShortTernaryOperatorSniff::CODE_REQUIRED_SHORT_TERNARY_OPERATOR);
		self::assertSniffError($report, 50, RequireShortTernaryOperatorSniff::CODE_REQUIRED_SHORT_TERNARY_OPERATOR);
		self::assertSniffError($report, 52, RequireShortTernaryOperatorSniff::CODE_REQUIRED_SHORT_TERNARY_OPERATOR);
		self::assertSniffError($report, 56, RequireShortTernaryOperatorSniff::CODE_REQUIRED_SHORT_TERNARY_OPERATOR);

		self::assertAllFixedInFile($report);
	}

	public function testNoErrorRecurrence(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireShortTernaryOperatorCloseTag.php', [
			'lineLengthLimit' => 120,
		]);
		self::assertNoSniffErrorInFile($report);
	}

}
