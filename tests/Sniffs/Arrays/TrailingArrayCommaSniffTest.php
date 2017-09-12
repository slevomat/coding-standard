<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Arrays;

class TrailingArrayCommaSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testCheckFile(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/trailingCommas.php');

		$this->assertSame(2, $report->getErrorCount());

		$this->assertSniffError($report, 18, TrailingArrayCommaSniff::CODE_MISSING_TRAILING_COMMA);
		$this->assertSniffError($report, 26, TrailingArrayCommaSniff::CODE_MISSING_TRAILING_COMMA);
	}

	public function testFixable(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/fixableTrailingCommas.php', [], [TrailingArrayCommaSniff::CODE_MISSING_TRAILING_COMMA]);
		$this->assertAllFixedInFile($report);
	}

}
