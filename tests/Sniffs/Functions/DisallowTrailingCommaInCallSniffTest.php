<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Functions;

use SlevomatCodingStandard\Sniffs\TestCase;

class DisallowTrailingCommaInCallSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowTrailingCommaInCallNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowTrailingCommaInCallErrors.php');

		self::assertSame(14, $report->getErrorCount());

		self::assertSniffError($report, 5, DisallowTrailingCommaInCallSniff::CODE_DISALLOWED_TRAILING_COMMA);
		self::assertSniffError($report, 12, DisallowTrailingCommaInCallSniff::CODE_DISALLOWED_TRAILING_COMMA);
		self::assertSniffError($report, 19, DisallowTrailingCommaInCallSniff::CODE_DISALLOWED_TRAILING_COMMA);
		self::assertSniffError($report, 29, DisallowTrailingCommaInCallSniff::CODE_DISALLOWED_TRAILING_COMMA);
		self::assertSniffError($report, 34, DisallowTrailingCommaInCallSniff::CODE_DISALLOWED_TRAILING_COMMA);
		self::assertSniffError($report, 39, DisallowTrailingCommaInCallSniff::CODE_DISALLOWED_TRAILING_COMMA);
		self::assertSniffError($report, 44, DisallowTrailingCommaInCallSniff::CODE_DISALLOWED_TRAILING_COMMA);
		self::assertSniffError($report, 48, DisallowTrailingCommaInCallSniff::CODE_DISALLOWED_TRAILING_COMMA);
		self::assertSniffError($report, 57, DisallowTrailingCommaInCallSniff::CODE_DISALLOWED_TRAILING_COMMA);
		self::assertSniffError($report, 62, DisallowTrailingCommaInCallSniff::CODE_DISALLOWED_TRAILING_COMMA);
		self::assertSniffError($report, 65, DisallowTrailingCommaInCallSniff::CODE_DISALLOWED_TRAILING_COMMA);
		self::assertSniffError($report, 75, DisallowTrailingCommaInCallSniff::CODE_DISALLOWED_TRAILING_COMMA);
		self::assertSniffError($report, 83, DisallowTrailingCommaInCallSniff::CODE_DISALLOWED_TRAILING_COMMA);
		self::assertSniffError($report, 91, DisallowTrailingCommaInCallSniff::CODE_DISALLOWED_TRAILING_COMMA);

		self::assertAllFixedInFile($report);
	}

}
