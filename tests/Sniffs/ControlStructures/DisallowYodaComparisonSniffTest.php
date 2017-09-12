<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

class DisallowYodaComparisonSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testNoErrors(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/disallowYodaComparisonNoErrors.php');
		$this->assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/disallowYodaComparisonErrors.php');
		foreach (range(3, 36) as $lineNumber) {
			$this->assertSniffError($report, $lineNumber, DisallowYodaComparisonSniff::CODE_DISALLOWED_YODA_COMPARISON);
		}
	}

	public function testFixable(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/fixableDisallowYodaComparisons.php', [], [DisallowYodaComparisonSniff::CODE_DISALLOWED_YODA_COMPARISON]);
		$this->assertAllFixedInFile($report);
	}

}
