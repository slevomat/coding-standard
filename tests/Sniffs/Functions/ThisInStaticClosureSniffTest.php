<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Functions;

use SlevomatCodingStandard\Sniffs\TestCase;

class ThisInStaticClosureSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/thisInStaticClosureNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/thisInStaticClosureErrors.php');

		self::assertSame(3, $report->getErrorCount());

		self::assertSniffError($report, 18, ThisInStaticClosureSniff::CODE_THIS_IN_STATIC_CLOSURE);
		self::assertSniffError($report, 25, ThisInStaticClosureSniff::CODE_THIS_IN_STATIC_CLOSURE);
		self::assertSniffError($report, 31, ThisInStaticClosureSniff::CODE_THIS_IN_STATIC_CLOSURE);
	}

}
