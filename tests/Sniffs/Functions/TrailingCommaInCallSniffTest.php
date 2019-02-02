<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Functions;

use SlevomatCodingStandard\Sniffs\TestCase;

class TrailingCommaInCallSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/trailingCommaInCallNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/trailingCommaInCallErrors.php');

		self::assertSame(11, $report->getErrorCount());

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

		self::assertAllFixedInFile($report);
	}

}
