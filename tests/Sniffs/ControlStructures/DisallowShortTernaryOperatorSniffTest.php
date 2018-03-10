<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

class DisallowShortTernaryOperatorSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testNoErrors(): void
	{
		self::assertNoSniffErrorInFile(self::checkFile(__DIR__ . '/data/disallowShortTernaryOperatorNoErrors.php'));
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowShortTernaryOperatorErrors.php');

		self::assertSame(3, $report->getErrorCount());

		self::assertSniffError($report, 3, DisallowShortTernaryOperatorSniff::CODE_DISALLOWED_SHORT_TERNARY_OPERATOR);
		self::assertSniffError($report, 5, DisallowShortTernaryOperatorSniff::CODE_DISALLOWED_SHORT_TERNARY_OPERATOR);
		self::assertSniffError($report, 7, DisallowShortTernaryOperatorSniff::CODE_DISALLOWED_SHORT_TERNARY_OPERATOR);

		$this->assertAllFixedInFile($report);
	}

}
