<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use SlevomatCodingStandard\Sniffs\TestCase;

class RequireNullCoalesceOperatorSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireNullCoalesceOperatorNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/requireNullCoalesceOperatorErrors.php',
			[],
			[RequireNullCoalesceOperatorSniff::CODE_NULL_COALESCE_OPERATOR_NOT_USED],
		);

		self::assertSame(49, $report->getErrorCount());

		foreach ([3, 5, 7, 9, 10, 12, 13, 15, 17, 18, 23, 30, 32, 34, 36, 38, 40, 41, 44, 47, 48, 49, 50, 51, 52, 53, 54, 56, 57, 61, 62, 63, 64, 67, 68, 69, 70, 72, 73, 74, 75, 76, 77, 78, 79, 81, 83, 85] as $line) {
			self::assertSniffError($report, $line, RequireNullCoalesceOperatorSniff::CODE_NULL_COALESCE_OPERATOR_NOT_USED);
		}

		self::assertAllFixedInFile($report);
	}

	/**
	 * Verifies that ternary expressions in named argument values are properly reported and converted to coalesce.
	 */
	public function testNamedArgumentsErrors(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/requireNullCoalesceOperatorNamedArgumentsErrors.php',
			[],
			[RequireNullCoalesceOperatorSniff::CODE_NULL_COALESCE_OPERATOR_NOT_USED],
		);

		self::assertSame(2, $report->getErrorCount());

		self::assertSniffError($report, 3, RequireNullCoalesceOperatorSniff::CODE_NULL_COALESCE_OPERATOR_NOT_USED);
		self::assertSniffError($report, 4, RequireNullCoalesceOperatorSniff::CODE_NULL_COALESCE_OPERATOR_NOT_USED);

		self::assertAllFixedInFile($report);
	}

}
