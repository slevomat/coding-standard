<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Arrays;

use SlevomatCodingStandard\Sniffs\TestCase;

class MultiLineArrayEndBracketPlacementSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/multiLineArrayEndBracketPlacementNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/multiLineArrayEndBracketPlacementErrors.php');

		self::assertSame(5, $report->getErrorCount());

		self::assertSniffError($report, 3, MultiLineArrayEndBracketPlacementSniff::CODE_ARRAY_END_WRONG_PLACEMENT);
		self::assertSniffError($report, 5, MultiLineArrayEndBracketPlacementSniff::CODE_ARRAY_END_WRONG_PLACEMENT);
		self::assertSniffError($report, 13, MultiLineArrayEndBracketPlacementSniff::CODE_ARRAY_END_WRONG_PLACEMENT);
		self::assertSniffError($report, 19, MultiLineArrayEndBracketPlacementSniff::CODE_ARRAY_END_WRONG_PLACEMENT);
		self::assertSniffError($report, 33, MultiLineArrayEndBracketPlacementSniff::CODE_ARRAY_END_WRONG_PLACEMENT);

		self::assertAllFixedInFile($report);
	}

}
