<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Exceptions;

use SlevomatCodingStandard\Sniffs\TestCase;

class RequireNonCapturingCatchSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireNonCapturingCatchNoErrors.php', [
			'enable' => true,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireNonCapturingCatchErrors.php', [
			'enable' => true,
		]);

		self::assertSame(2, $report->getErrorCount());

		self::assertSniffError($report, 5, RequireNonCapturingCatchSniff::CODE_NON_CAPTURING_CATCH_REQUIRED);
		self::assertSniffError($report, 11, RequireNonCapturingCatchSniff::CODE_NON_CAPTURING_CATCH_REQUIRED);

		self::assertAllFixedInFile($report);
	}

	public function testShouldNotReportIfSniffIsDisabled(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireNonCapturingCatchWhenDisabled.php', [
			'enable' => false,
		]);

		self::assertSame(0, $report->getErrorCount());
		self::assertNoSniffErrorInFile($report);
	}

}
