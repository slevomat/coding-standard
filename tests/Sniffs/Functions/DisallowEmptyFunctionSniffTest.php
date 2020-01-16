<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Functions;

use SlevomatCodingStandard\Sniffs\TestCase;

class DisallowEmptyFunctionSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowEmptyFunctionNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowEmptyFunctionErrors.php');

		self::assertSame(5, $report->getErrorCount());

		foreach ([3, 10, 12, 26, 29] as $line) {
			self::assertSniffError($report, $line, DisallowEmptyFunctionSniff::CODE_EMPTY_FUNCTION);
		}
	}

}
