<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Functions;

use SlevomatCodingStandard\Sniffs\TestCase;

class RequireTrailingCommaInCallSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireTrailingCommaInCallNoErrors.php', [
			'enable' => true,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireTrailingCommaInCallErrors.php', [
			'enable' => true,
		]);

		self::assertSame(14, $report->getErrorCount());

		self::assertSniffError($report, 5, RequireTrailingCommaInCallSniff::CODE_MISSING_TRAILING_COMMA);
		self::assertSniffError($report, 12, RequireTrailingCommaInCallSniff::CODE_MISSING_TRAILING_COMMA);
		self::assertSniffError($report, 19, RequireTrailingCommaInCallSniff::CODE_MISSING_TRAILING_COMMA);
		self::assertSniffError($report, 29, RequireTrailingCommaInCallSniff::CODE_MISSING_TRAILING_COMMA);
		self::assertSniffError($report, 34, RequireTrailingCommaInCallSniff::CODE_MISSING_TRAILING_COMMA);
		self::assertSniffError($report, 39, RequireTrailingCommaInCallSniff::CODE_MISSING_TRAILING_COMMA);
		self::assertSniffError($report, 44, RequireTrailingCommaInCallSniff::CODE_MISSING_TRAILING_COMMA);
		self::assertSniffError($report, 48, RequireTrailingCommaInCallSniff::CODE_MISSING_TRAILING_COMMA);
		self::assertSniffError($report, 57, RequireTrailingCommaInCallSniff::CODE_MISSING_TRAILING_COMMA);
		self::assertSniffError($report, 62, RequireTrailingCommaInCallSniff::CODE_MISSING_TRAILING_COMMA);
		self::assertSniffError($report, 65, RequireTrailingCommaInCallSniff::CODE_MISSING_TRAILING_COMMA);
		self::assertSniffError($report, 75, RequireTrailingCommaInCallSniff::CODE_MISSING_TRAILING_COMMA);
		self::assertSniffError($report, 83, RequireTrailingCommaInCallSniff::CODE_MISSING_TRAILING_COMMA);
		self::assertSniffError($report, 91, RequireTrailingCommaInCallSniff::CODE_MISSING_TRAILING_COMMA);

		self::assertAllFixedInFile($report);
	}

	public function testShouldNotReportIfSniffIsDisabled(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireTrailingCommaInCallErrors.php', [
			'enable' => false,
		]);

		self::assertNoSniffErrorInFile($report);
	}

}
