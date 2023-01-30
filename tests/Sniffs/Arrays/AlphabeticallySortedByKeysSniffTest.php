<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Arrays;

use SlevomatCodingStandard\Sniffs\TestCase;

class AlphabeticallySortedByKeysSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/alphabeticallySortedByKeysNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/alphabeticallySortedByKeysErrors.php', []);

		self::assertSame(4, $report->getErrorCount());

		self::assertSniffError($report, 3, AlphabeticallySortedByKeysSniff::CODE_INCORRECT_KEY_ORDER);
		self::assertSniffError($report, 15, AlphabeticallySortedByKeysSniff::CODE_INCORRECT_KEY_ORDER);
		self::assertSniffError($report, 21, AlphabeticallySortedByKeysSniff::CODE_INCORRECT_KEY_ORDER);
		self::assertSniffError($report, 25, AlphabeticallySortedByKeysSniff::CODE_INCORRECT_KEY_ORDER);

		self::assertAllFixedInFile($report);
	}

}
