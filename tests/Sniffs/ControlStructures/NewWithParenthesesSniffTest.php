<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

class NewWithParenthesesSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testNoErrors(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/newWithParenthesesNoErrors.php');
		$this->assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/newWithParenthesesErrors.php', [], [NewWithParenthesesSniff::CODE_MISSING_PARENTHESES]);

		$this->assertSame(19, $report->getErrorCount());

		foreach ([3, 9, 17, 22, 25, 29, 35, 37, 40, 43, 45, 47, 48, 50, 52, 57] as $line) {
			$this->assertSniffError($report, $line, NewWithParenthesesSniff::CODE_MISSING_PARENTHESES);
		}

		$this->assertAllFixedInFile($report);
	}

}
