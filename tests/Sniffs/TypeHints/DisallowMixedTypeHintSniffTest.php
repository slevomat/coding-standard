<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use SlevomatCodingStandard\Sniffs\TestCase;

class DisallowMixedTypeHintSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowMixedTypeHintNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowMixedTypeHintErrors.php');

		self::assertSame(14, $report->getErrorCount());

		self::assertSniffError($report, 4, DisallowMixedTypeHintSniff::CODE_DISALLOWED_MIXED_TYPE_HINT);
		self::assertSniffError($report, 9, DisallowMixedTypeHintSniff::CODE_DISALLOWED_MIXED_TYPE_HINT);
		self::assertSniffError($report, 12, DisallowMixedTypeHintSniff::CODE_DISALLOWED_MIXED_TYPE_HINT);
		self::assertSniffError($report, 15, DisallowMixedTypeHintSniff::CODE_DISALLOWED_MIXED_TYPE_HINT);
		self::assertSniffError($report, 18, DisallowMixedTypeHintSniff::CODE_DISALLOWED_MIXED_TYPE_HINT);
		self::assertSniffError($report, 21, DisallowMixedTypeHintSniff::CODE_DISALLOWED_MIXED_TYPE_HINT);
		self::assertSniffError($report, 24, DisallowMixedTypeHintSniff::CODE_DISALLOWED_MIXED_TYPE_HINT);
		self::assertSniffError($report, 27, DisallowMixedTypeHintSniff::CODE_DISALLOWED_MIXED_TYPE_HINT);
		self::assertSniffError($report, 30, DisallowMixedTypeHintSniff::CODE_DISALLOWED_MIXED_TYPE_HINT);
		self::assertSniffError($report, 39, DisallowMixedTypeHintSniff::CODE_DISALLOWED_MIXED_TYPE_HINT);
		self::assertSniffError($report, 48, DisallowMixedTypeHintSniff::CODE_DISALLOWED_MIXED_TYPE_HINT);
		self::assertSniffError($report, 51, DisallowMixedTypeHintSniff::CODE_DISALLOWED_MIXED_TYPE_HINT);
	}

}
