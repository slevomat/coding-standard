<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Arrays;

use SlevomatCodingStandard\Sniffs\TestCase;

class MultiLineArrayEndBracketPlacementSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		self::assertNoSniffErrorInFile(self::checkFile(
			__DIR__ . '/data/MultiLineArrayEndBracketPlacementSniffNoErrors.php'
		));
	}

	public function testErrors(): void
	{
		$file = self::checkFile(__DIR__ . '/data/MultiLineArrayEndBracketPlacementSniffErrors.php');

		self::assertSame(5, $file->getErrorCount());

		self::assertSniffError($file, 3, MultiLineArrayEndBracketPlacementSniff::CODE_ARRAY_END_WRONG_PLACEMENT);
		self::assertSniffError($file, 5, MultiLineArrayEndBracketPlacementSniff::CODE_ARRAY_END_WRONG_PLACEMENT);
		self::assertSniffError($file, 13, MultiLineArrayEndBracketPlacementSniff::CODE_ARRAY_END_WRONG_PLACEMENT);
		self::assertSniffError($file, 19, MultiLineArrayEndBracketPlacementSniff::CODE_ARRAY_END_WRONG_PLACEMENT);
		self::assertSniffError($file, 33, MultiLineArrayEndBracketPlacementSniff::CODE_ARRAY_END_WRONG_PLACEMENT);

		self::assertAllFixedInFile($file);
	}

}
