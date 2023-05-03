<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Arrays;

use SlevomatCodingStandard\Sniffs\TestCase;

class ArrayAccessSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/arrayAccessNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/arrayAccessErrors.php');

		self::assertSame(4, $report->getErrorCount());

		self::assertSniffError($report, 5, ArrayAccessSniff::CODE_NO_SPACE_BEFORE_BRACKETS);
		self::assertSniffError($report, 6, ArrayAccessSniff::CODE_NO_SPACE_BEFORE_BRACKETS);
		self::assertSniffError($report, 7, ArrayAccessSniff::CODE_NO_SPACE_BETWEEN_BRACKETS);
		self::assertSniffError($report, 9, ArrayAccessSniff::CODE_NO_SPACE_BEFORE_BRACKETS);

		self::assertAllFixedInFile($report);
	}

}
