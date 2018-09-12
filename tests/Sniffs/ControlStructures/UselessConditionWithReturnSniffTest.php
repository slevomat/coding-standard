<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use SlevomatCodingStandard\Sniffs\TestCase;
use Throwable;

class UselessConditionWithReturnSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/uselessConditionWithReturnNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/uselessConditionWithReturnErrors.php');

		self::assertSame(14, $report->getErrorCount());

		self::assertSniffError($report, 4, UselessConditionWithReturnSniff::CODE_USELESS_IF_CONDITION);
		self::assertSniffError($report, 12, UselessConditionWithReturnSniff::CODE_USELESS_IF_CONDITION);
		self::assertSniffError($report, 20, UselessConditionWithReturnSniff::CODE_USELESS_IF_CONDITION);
		self::assertSniffError($report, 28, UselessConditionWithReturnSniff::CODE_USELESS_IF_CONDITION);
		self::assertSniffError($report, 36, UselessConditionWithReturnSniff::CODE_USELESS_IF_CONDITION);
		self::assertSniffError($report, 44, UselessConditionWithReturnSniff::CODE_USELESS_IF_CONDITION);
		self::assertSniffError($report, 52, UselessConditionWithReturnSniff::CODE_USELESS_IF_CONDITION);
		self::assertSniffError($report, 85, UselessConditionWithReturnSniff::CODE_USELESS_IF_CONDITION);

		self::assertSniffError($report, 60, UselessConditionWithReturnSniff::CODE_USELESS_INLINE_IF_CONDITION);
		self::assertSniffError($report, 64, UselessConditionWithReturnSniff::CODE_USELESS_INLINE_IF_CONDITION);
		self::assertSniffError($report, 68, UselessConditionWithReturnSniff::CODE_USELESS_INLINE_IF_CONDITION);
		self::assertSniffError($report, 72, UselessConditionWithReturnSniff::CODE_USELESS_INLINE_IF_CONDITION);
		self::assertSniffError($report, 76, UselessConditionWithReturnSniff::CODE_USELESS_INLINE_IF_CONDITION);
		self::assertSniffError($report, 80, UselessConditionWithReturnSniff::CODE_USELESS_INLINE_IF_CONDITION);

		self::assertAllFixedInFile($report);
	}

	public function testIfWithoutCurlyBraces(): void
	{
		self::expectException(Throwable::class);
		self::expectExceptionMessage('"if" without curly braces is not supported.');
		self::checkFile(__DIR__ . '/data/uselessConditionWithReturnIfWithoutCurlyBraces.php');
	}

	public function testElseWithoutCurlyBraces(): void
	{
		self::expectException(Throwable::class);
		self::expectExceptionMessage('"else" without curly braces is not supported.');
		self::checkFile(__DIR__ . '/data/uselessConditionWithReturnElseWithoutCurlyBraces.php');
	}

}
