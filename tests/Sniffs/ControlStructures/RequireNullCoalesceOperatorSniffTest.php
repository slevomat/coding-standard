<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

class RequireNullCoalesceOperatorSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireNullCoalesceOperatorNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireNullCoalesceOperatorErrors.php', [], [RequireNullCoalesceOperatorSniff::CODE_NULL_COALESCE_OPERATOR_NOT_USED]);

		self::assertSame(3, $report->getErrorCount());

		foreach ([3, 5, 7] as $line) {
			self::assertSniffError($report, $line, RequireNullCoalesceOperatorSniff::CODE_NULL_COALESCE_OPERATOR_NOT_USED);
		}

		self::assertAllFixedInFile($report);
	}

}
