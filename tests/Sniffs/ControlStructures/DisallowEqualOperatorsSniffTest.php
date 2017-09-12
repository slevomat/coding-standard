<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

class DisallowEqualOperatorsSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testNoErrors(): void
	{
		$this->assertNoSniffErrorInFile($this->checkFile(__DIR__ . '/data/disallowEqualOperatorsNoErrors.php'));
	}

	public function testErrors(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/disallowEqualOperatorsErrors.php');

		$this->assertSame(4, $report->getErrorCount());

		$this->assertSniffError($report, 3, DisallowEqualOperatorsSniff::CODE_DISALLOWED_EQUAL_OPERATOR);
		$this->assertSniffError($report, 4, DisallowEqualOperatorsSniff::CODE_DISALLOWED_EQUAL_OPERATOR);
		$this->assertSniffError($report, 5, DisallowEqualOperatorsSniff::CODE_DISALLOWED_NOT_EQUAL_OPERATOR);
		$this->assertSniffError($report, 6, DisallowEqualOperatorsSniff::CODE_DISALLOWED_NOT_EQUAL_OPERATOR);

		$this->assertAllFixedInFile($report);
	}

}
