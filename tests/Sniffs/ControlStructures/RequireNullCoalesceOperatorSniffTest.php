<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

class RequireNullCoalesceOperatorSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testNoErrors(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/requireNullCoalesceOperatorNoErrors.php');
		$this->assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/requireNullCoalesceOperatorErrors.php', [], [RequireNullCoalesceOperatorSniff::CODE_NULL_COALESCE_OPERATOR_NOT_USED]);

		$this->assertSame(3, $report->getErrorCount());

		foreach ([3, 5, 7] as $line) {
			$this->assertSniffError($report, $line, RequireNullCoalesceOperatorSniff::CODE_NULL_COALESCE_OPERATOR_NOT_USED);
		}

		$this->assertAllFixedInFile($report);
	}

}
