<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Functions;

use SlevomatCodingStandard\Sniffs\TestCase;

class MaxNestingLevelSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/maxNestingLevelNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/maxNestingLevelErrors.php');

		self::assertSame(1, $report->getErrorCount());

		self::assertSniffError($report, 3, MaxNestingLevelSniff::CODE_MAX_NESTING_LEVEL);
	}

}
