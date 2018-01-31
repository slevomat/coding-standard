<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Arrays;

class TrailingArrayCommaSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testCheckFile(): void
	{
		$report = self::checkFile(__DIR__ . '/data/trailingCommas.php');

		self::assertSame(2, $report->getErrorCount());

		self::assertSniffError($report, 18, TrailingArrayCommaSniff::CODE_MISSING_TRAILING_COMMA);
		self::assertSniffError($report, 26, TrailingArrayCommaSniff::CODE_MISSING_TRAILING_COMMA);
	}

	public function testFixable(): void
	{
		$report = self::checkFile(__DIR__ . '/data/fixableTrailingCommas.php', [], [TrailingArrayCommaSniff::CODE_MISSING_TRAILING_COMMA]);
		self::assertAllFixedInFile($report);
	}

}
