<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Functions;

use SlevomatCodingStandard\Sniffs\TestCase;

class TrailingCommaInCallSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/trailingCommaInCallNoErrors.php', [
			'enable' => true,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/trailingCommaInCallErrors.php', [
			'enable' => true,
		]);

		self::assertSame(14, $report->getErrorCount());

		self::assertSniffError($report, 5, TrailingCommaInCallSniff::CODE_MISSING_TRAILING_COMMA);
		self::assertSniffError($report, 12, TrailingCommaInCallSniff::CODE_MISSING_TRAILING_COMMA);
		self::assertSniffError($report, 19, TrailingCommaInCallSniff::CODE_MISSING_TRAILING_COMMA);
		self::assertSniffError($report, 29, TrailingCommaInCallSniff::CODE_MISSING_TRAILING_COMMA);
		self::assertSniffError($report, 34, TrailingCommaInCallSniff::CODE_MISSING_TRAILING_COMMA);
		self::assertSniffError($report, 39, TrailingCommaInCallSniff::CODE_MISSING_TRAILING_COMMA);
		self::assertSniffError($report, 44, TrailingCommaInCallSniff::CODE_MISSING_TRAILING_COMMA);
		self::assertSniffError($report, 48, TrailingCommaInCallSniff::CODE_MISSING_TRAILING_COMMA);
		self::assertSniffError($report, 57, TrailingCommaInCallSniff::CODE_MISSING_TRAILING_COMMA);
		self::assertSniffError($report, 62, TrailingCommaInCallSniff::CODE_MISSING_TRAILING_COMMA);
		self::assertSniffError($report, 65, TrailingCommaInCallSniff::CODE_MISSING_TRAILING_COMMA);
		self::assertSniffError($report, 75, TrailingCommaInCallSniff::CODE_MISSING_TRAILING_COMMA);
		self::assertSniffError($report, 83, TrailingCommaInCallSniff::CODE_MISSING_TRAILING_COMMA);
		self::assertSniffError($report, 91, TrailingCommaInCallSniff::CODE_MISSING_TRAILING_COMMA);

		self::assertAllFixedInFile($report);
	}

	public function testShouldNotReportIfSniffIsDisabled(): void
	{
		$report = self::checkFile(__DIR__ . '/data/trailingCommaInCallErrors.php', [
			'enable' => false,
		]);

		self::assertSame(0, $report->getErrorCount());
		self::assertNoSniffErrorInFile($report);
	}

}
