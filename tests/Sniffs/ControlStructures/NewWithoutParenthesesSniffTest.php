<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use SlevomatCodingStandard\Sniffs\TestCase;

class NewWithoutParenthesesSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/newWithoutParenthesesNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/newWithoutParenthesesErrors.php');

		self::assertSame(20, $report->getErrorCount());

		foreach ([3, 9, 17, 22, 25, 27, 28, 29, 32, 35, 38, 40, 42, 43, 45, 47, 52] as $line) {
			self::assertSniffError($report, $line, NewWithoutParenthesesSniff::CODE_USELESS_PARENTHESES);
		}

		self::assertAllFixedInFile($report);
	}

}
