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
			[RequireNullCoalesceOperatorSniff::CODE_NULL_COALESCE_OPERATOR_NOT_USED]
		);

		self::assertSame(17, $report->getErrorCount());

		foreach ([3, 5, 7, 9, 10, 12, 13, 15, 17, 18, 23, 30, 32, 34, 36, 38, 41] as $line) {
			self::assertSniffError($report, $line, RequireNullCoalesceOperatorSniff::CODE_NULL_COALESCE_OPERATOR_NOT_USED);
		}

		self::assertAllFixedInFile($report);
	}

}
