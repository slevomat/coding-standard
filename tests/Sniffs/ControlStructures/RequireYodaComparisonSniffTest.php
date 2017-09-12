<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

class RequireYodaComparisonSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testNoErrors(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/requireYodaComparisonNoErrors.php');
		$this->assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/requireYodaComparisonErrors.php');
		foreach (range(3, 36) as $lineNumber) {
			$this->assertSniffError($report, $lineNumber, RequireYodaComparisonSniff::CODE_REQUIRED_YODA_COMPARISON);
		}
	}

	public function testFixable(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/fixableRequireYodaComparisons.php', [], [RequireYodaComparisonSniff::CODE_REQUIRED_YODA_COMPARISON]);
		$this->assertAllFixedInFile($report);
	}

}
