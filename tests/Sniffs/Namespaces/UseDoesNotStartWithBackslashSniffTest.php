<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

class UseDoesNotStartWithBackslashSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testNoErrors(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/useDoesNotStartWithBackslashNoErrors.php');
		$this->assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/useDoesNotStartWithBackslashErrors.php');

		$this->assertSame(3, $report->getErrorCount());

		$this->assertSniffError($report, 3, UseDoesNotStartWithBackslashSniff::CODE_STARTS_WITH_BACKSLASH);
		$this->assertSniffError($report, 4, UseDoesNotStartWithBackslashSniff::CODE_STARTS_WITH_BACKSLASH);
		$this->assertSniffError($report, 5, UseDoesNotStartWithBackslashSniff::CODE_STARTS_WITH_BACKSLASH);
	}

	public function testFixable(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/fixableUseDoesNotStartWithBackslash.php', [], [UseDoesNotStartWithBackslashSniff::CODE_STARTS_WITH_BACKSLASH]);
		$this->assertAllFixedInFile($report);
	}

}
