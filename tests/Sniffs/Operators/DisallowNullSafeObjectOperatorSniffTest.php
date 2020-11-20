<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Operators;

use SlevomatCodingStandard\Sniffs\TestCase;

class DisallowNullSafeObjectOperatorSniffTest extends TestCase
{

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowNullSafeObjectOperatorErrors.php');

		self::assertSame(1, $report->getErrorCount());

		self::assertSniffError($report, 3, DisallowNullSafeObjectOperatorSniff::CODE_DISALLOWED_NULL_SAFE_OBJECT_OPERATOR);
	}

}
