<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use SlevomatCodingStandard\Sniffs\TestCase;

class UselessTernaryOperatorSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/uselessTernaryOperatorNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/uselessTernaryOperatorErrors.php');

		self::assertSame(37, $report->getErrorCount());

		self::assertSniffError($report, 4, UselessTernaryOperatorSniff::CODE_USELESS_TERNARY_OPERATOR);
		self::assertSniffError($report, 8, UselessTernaryOperatorSniff::CODE_USELESS_TERNARY_OPERATOR);
		self::assertSniffError($report, 12, UselessTernaryOperatorSniff::CODE_USELESS_TERNARY_OPERATOR);
		self::assertSniffError($report, 16, UselessTernaryOperatorSniff::CODE_USELESS_TERNARY_OPERATOR);
		self::assertSniffError($report, 20, UselessTernaryOperatorSniff::CODE_USELESS_TERNARY_OPERATOR);
		self::assertSniffError($report, 24, UselessTernaryOperatorSniff::CODE_USELESS_TERNARY_OPERATOR);
		self::assertSniffError($report, 30, UselessTernaryOperatorSniff::CODE_USELESS_TERNARY_OPERATOR);
		self::assertSniffError($report, 36, UselessTernaryOperatorSniff::CODE_USELESS_TERNARY_OPERATOR);
		self::assertSniffError($report, 43, UselessTernaryOperatorSniff::CODE_USELESS_TERNARY_OPERATOR);
		self::assertSniffError($report, 49, UselessTernaryOperatorSniff::CODE_USELESS_TERNARY_OPERATOR);
		self::assertSniffError($report, 55, UselessTernaryOperatorSniff::CODE_USELESS_TERNARY_OPERATOR);
		self::assertSniffError($report, 59, UselessTernaryOperatorSniff::CODE_USELESS_TERNARY_OPERATOR);
		self::assertSniffError($report, 65, UselessTernaryOperatorSniff::CODE_USELESS_TERNARY_OPERATOR);
		self::assertSniffError($report, 71, UselessTernaryOperatorSniff::CODE_USELESS_TERNARY_OPERATOR);
		self::assertSniffError($report, 75, UselessTernaryOperatorSniff::CODE_USELESS_TERNARY_OPERATOR);
		self::assertSniffError($report, 79, UselessTernaryOperatorSniff::CODE_USELESS_TERNARY_OPERATOR);
		self::assertSniffError($report, 83, UselessTernaryOperatorSniff::CODE_USELESS_TERNARY_OPERATOR);
		self::assertSniffError($report, 87, UselessTernaryOperatorSniff::CODE_USELESS_TERNARY_OPERATOR);
		self::assertSniffError($report, 91, UselessTernaryOperatorSniff::CODE_USELESS_TERNARY_OPERATOR);
		self::assertSniffError($report, 95, UselessTernaryOperatorSniff::CODE_USELESS_TERNARY_OPERATOR);
		self::assertSniffError($report, 99, UselessTernaryOperatorSniff::CODE_USELESS_TERNARY_OPERATOR);
		self::assertSniffError($report, 103, UselessTernaryOperatorSniff::CODE_USELESS_TERNARY_OPERATOR);
		self::assertSniffError($report, 107, UselessTernaryOperatorSniff::CODE_USELESS_TERNARY_OPERATOR);
		self::assertSniffError($report, 111, UselessTernaryOperatorSniff::CODE_USELESS_TERNARY_OPERATOR);
		self::assertSniffError($report, 115, UselessTernaryOperatorSniff::CODE_USELESS_TERNARY_OPERATOR);
		self::assertSniffError($report, 119, UselessTernaryOperatorSniff::CODE_USELESS_TERNARY_OPERATOR);
		self::assertSniffError($report, 123, UselessTernaryOperatorSniff::CODE_USELESS_TERNARY_OPERATOR);
		self::assertSniffError($report, 127, UselessTernaryOperatorSniff::CODE_USELESS_TERNARY_OPERATOR);
		self::assertSniffError($report, 131, UselessTernaryOperatorSniff::CODE_USELESS_TERNARY_OPERATOR);
		self::assertSniffError($report, 135, UselessTernaryOperatorSniff::CODE_USELESS_TERNARY_OPERATOR);
		self::assertSniffError($report, 138, UselessTernaryOperatorSniff::CODE_USELESS_TERNARY_OPERATOR);
		self::assertSniffError($report, 139, UselessTernaryOperatorSniff::CODE_USELESS_TERNARY_OPERATOR);
		self::assertSniffError($report, 140, UselessTernaryOperatorSniff::CODE_USELESS_TERNARY_OPERATOR);
		self::assertSniffError($report, 141, UselessTernaryOperatorSniff::CODE_USELESS_TERNARY_OPERATOR);
		self::assertSniffError($report, 142, UselessTernaryOperatorSniff::CODE_USELESS_TERNARY_OPERATOR);
		self::assertSniffError($report, 143, UselessTernaryOperatorSniff::CODE_USELESS_TERNARY_OPERATOR);
		self::assertSniffError($report, 144, UselessTernaryOperatorSniff::CODE_USELESS_TERNARY_OPERATOR);

		self::assertAllFixedInFile($report);
	}

	/**
	 * Verifies that useless ternary expressions inside match arms are reported and fixed.
	 */
	public function testMatchErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/uselessTernaryOperatorMatchErrors.php');

		self::assertSame(5, $report->getErrorCount());

		self::assertSniffError($report, 4, UselessTernaryOperatorSniff::CODE_USELESS_TERNARY_OPERATOR);
		self::assertSniffError($report, 8, UselessTernaryOperatorSniff::CODE_USELESS_TERNARY_OPERATOR);
		self::assertSniffError($report, 12, UselessTernaryOperatorSniff::CODE_USELESS_TERNARY_OPERATOR);
		self::assertSniffError($report, 16, UselessTernaryOperatorSniff::CODE_USELESS_TERNARY_OPERATOR);
		self::assertSniffError($report, 20, UselessTernaryOperatorSniff::CODE_USELESS_TERNARY_OPERATOR);

		self::assertAllFixedInFile($report);
	}

	public function testErrorsWithAssumeAllConditionExpressionsAreAlreadyBooleanEnabled(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/uselessTernaryOperatorErrorsWithAssumeAllConditionExpressionsAreAlreadyBooleanEnabled.php',
			[
				'assumeAllConditionExpressionsAreAlreadyBoolean' => true,
			],
		);

		self::assertSame(2, $report->getErrorCount());

		self::assertSniffError($report, 4, UselessTernaryOperatorSniff::CODE_USELESS_TERNARY_OPERATOR);
		self::assertSniffError($report, 8, UselessTernaryOperatorSniff::CODE_USELESS_TERNARY_OPERATOR);

		self::assertAllFixedInFile($report);
	}

}
