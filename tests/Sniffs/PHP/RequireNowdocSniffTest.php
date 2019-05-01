<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\PHP;

use SlevomatCodingStandard\Sniffs\TestCase;

class RequireNowdocSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireNowdocNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireNowdocErrors.php');

		self::assertSame(3, $report->getErrorCount());

		self::assertSniffError($report, 5, RequireNowdocSniff::CODE_REQUIRED_NOWDOC);
		self::assertSniffError($report, 9, RequireNowdocSniff::CODE_REQUIRED_NOWDOC);
		self::assertSniffError($report, 13, RequireNowdocSniff::CODE_REQUIRED_NOWDOC);

		self::assertAllFixedInFile($report);
	}

}
