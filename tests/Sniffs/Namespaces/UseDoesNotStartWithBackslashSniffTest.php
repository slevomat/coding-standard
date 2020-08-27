<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use SlevomatCodingStandard\Sniffs\TestCase;

class UseDoesNotStartWithBackslashSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/useDoesNotStartWithBackslashNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/useDoesNotStartWithBackslashErrors.php');

		self::assertSame(3, $report->getErrorCount());

		self::assertSniffError($report, 3, UseDoesNotStartWithBackslashSniff::CODE_STARTS_WITH_BACKSLASH);
		self::assertSniffError($report, 4, UseDoesNotStartWithBackslashSniff::CODE_STARTS_WITH_BACKSLASH);
		self::assertSniffError($report, 5, UseDoesNotStartWithBackslashSniff::CODE_STARTS_WITH_BACKSLASH);
	}

	public function testFixable(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/fixableUseDoesNotStartWithBackslash.php',
			[],
			[UseDoesNotStartWithBackslashSniff::CODE_STARTS_WITH_BACKSLASH]
		);
		self::assertAllFixedInFile($report);
	}

}
