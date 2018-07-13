<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use SlevomatCodingStandard\Sniffs\TestCase;

class DisallowEmptySniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowEmptyNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowEmptyErrors.php');

		self::assertSame(2, $report->getErrorCount());

		self::assertSniffError($report, 3, DisallowEmptySniff::CODE_DISALLOWED_EMPTY);
		self::assertSniffError($report, 5, DisallowEmptySniff::CODE_DISALLOWED_EMPTY);
	}

}
