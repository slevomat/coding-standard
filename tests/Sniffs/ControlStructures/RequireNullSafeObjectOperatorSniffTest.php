<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use SlevomatCodingStandard\Sniffs\TestCase;

class RequireNullSafeObjectOperatorSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireNullSafeObjectOperatorNoErrors.php', [
			'enable' => true,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireNullSafeObjectOperatorErrors.php', [
			'enable' => true,
		]);

		self::assertSame(15, $report->getErrorCount());

		foreach ([3, 4, 6, 7, 9, 10, 12, 13, 15, 18, 27] as $line) {
			self::assertSniffError($report, $line, RequireNullSafeObjectOperatorSniff::CODE_REQUIRED_NULL_SAFE_OBJECT_OPERATOR);
		}

		self::assertCount(2, $report->getErrors()[12]);
		self::assertCount(2, $report->getErrors()[13]);

		self::assertAllFixedInFile($report);
	}

	public function testShouldNotReportIfSniffIsDisabled(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireNullSafeObjectOperatorErrors.php', [
			'enable' => false,
		]);

		self::assertNoSniffErrorInFile($report);
	}

}
