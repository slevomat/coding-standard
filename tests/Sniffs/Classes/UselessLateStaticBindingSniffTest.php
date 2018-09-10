<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use SlevomatCodingStandard\Sniffs\TestCase;

class UselessLateStaticBindingSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/uselessLateStaticBindingNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/uselessLateStaticBindingErrors.php');

		self::assertSame(3, $report->getErrorCount());

		self::assertSniffError($report, 8, UselessLateStaticBindingSniff::CODE_USELESS_LATE_STATIC_BINDING);
		self::assertSniffError($report, 13, UselessLateStaticBindingSniff::CODE_USELESS_LATE_STATIC_BINDING);
		self::assertSniffError($report, 18, UselessLateStaticBindingSniff::CODE_USELESS_LATE_STATIC_BINDING);

		self::assertAllFixedInFile($report);
	}

}
