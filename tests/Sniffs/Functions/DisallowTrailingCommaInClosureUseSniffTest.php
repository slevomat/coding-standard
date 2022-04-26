<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Functions;

use SlevomatCodingStandard\Sniffs\TestCase;

class DisallowTrailingCommaInClosureUseSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowTrailingCommaInClosureUseNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowTrailingCommaInClosureUseErrors.php');

		self::assertSame(1, $report->getErrorCount());

		self::assertSniffError($report, 5, DisallowTrailingCommaInClosureUseSniff::CODE_DISALLOWED_TRAILING_COMMA);

		self::assertAllFixedInFile($report);
	}

}
