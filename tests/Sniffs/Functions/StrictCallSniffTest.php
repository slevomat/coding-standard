<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Functions;

use SlevomatCodingStandard\Sniffs\TestCase;

class StrictCallSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/strictCallNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/strictCallErrors.php');

		self::assertSame(4, $report->getErrorCount());

		self::assertSniffError($report, 3, StrictCallSniff::CODE_STRICT_PARAMETER_MISSING);
		self::assertSniffError($report, 4, StrictCallSniff::CODE_STRICT_PARAMETER_MISSING);

		self::assertSniffError($report, 6, StrictCallSniff::CODE_NON_STRICT_COMPARISON);
		self::assertSniffError($report, 7, StrictCallSniff::CODE_NON_STRICT_COMPARISON);
	}

}
