<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

class EarlyExitSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testNoErrors(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/earlyExitNoErrors.php');
		$this->assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/earlyExitErrors.php', [], [EarlyExitSniff::CODE_EARLY_EXIT_NOT_USED]);

		$this->assertSame(8, $report->getErrorCount());

		foreach ([6, 14, 22, 30, 38, 46, 54, 62] as $line) {
			$this->assertSniffError($report, $line, EarlyExitSniff::CODE_EARLY_EXIT_NOT_USED);
		}
	}

}
