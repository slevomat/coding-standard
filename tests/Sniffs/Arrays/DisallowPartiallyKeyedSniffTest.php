<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Arrays;

use SlevomatCodingStandard\Sniffs\TestCase;

class DisallowPartiallyKeyedSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowPartiallyKeyedNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowPartiallyKeyedErrors.php', []);

		self::assertSame(2, $report->getErrorCount());

		self::assertSniffError($report, 3, DisallowPartiallyKeyedSniff::CODE_DISALLOWED_PARTIALLY_KEYED);
		self::assertSniffError($report, 10, DisallowPartiallyKeyedSniff::CODE_DISALLOWED_PARTIALLY_KEYED);
	}

}
