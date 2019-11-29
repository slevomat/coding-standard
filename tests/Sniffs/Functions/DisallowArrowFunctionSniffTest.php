<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Functions;

use SlevomatCodingStandard\Sniffs\TestCase;

class DisallowArrowFunctionSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowArrowFunctionNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowArrowFunctionErrors.php');

		self::assertSame(1, $report->getErrorCount());

		self::assertSniffError($report, 3, DisallowArrowFunctionSniff::CODE_DISALLOWED_ARROW_FUNCTION);
	}

}
