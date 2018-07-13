<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use SlevomatCodingStandard\Sniffs\TestCase;

class RequireShortTernaryOperatorSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		self::assertNoSniffErrorInFile(self::checkFile(__DIR__ . '/data/requireShortTernaryOperatorNoErrors.php'));
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireShortTernaryOperatorErrors.php');

		self::assertSame(8, $report->getErrorCount());

		self::assertSniffError($report, 3, RequireShortTernaryOperatorSniff::CODE_REQUIRED_SHORT_TERNARY_OPERATOR);
		self::assertSniffError($report, 5, RequireShortTernaryOperatorSniff::CODE_REQUIRED_SHORT_TERNARY_OPERATOR);
		self::assertSniffError($report, 7, RequireShortTernaryOperatorSniff::CODE_REQUIRED_SHORT_TERNARY_OPERATOR);
		self::assertSniffError($report, 10, RequireShortTernaryOperatorSniff::CODE_REQUIRED_SHORT_TERNARY_OPERATOR);
		self::assertSniffError($report, 14, RequireShortTernaryOperatorSniff::CODE_REQUIRED_SHORT_TERNARY_OPERATOR);
		self::assertSniffError($report, 15, RequireShortTernaryOperatorSniff::CODE_REQUIRED_SHORT_TERNARY_OPERATOR);
		self::assertSniffError($report, 18, RequireShortTernaryOperatorSniff::CODE_REQUIRED_SHORT_TERNARY_OPERATOR);
		self::assertSniffError($report, 20, RequireShortTernaryOperatorSniff::CODE_REQUIRED_SHORT_TERNARY_OPERATOR);

		$this->assertAllFixedInFile($report);
	}

}
