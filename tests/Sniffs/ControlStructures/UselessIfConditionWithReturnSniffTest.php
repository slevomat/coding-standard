<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use SlevomatCodingStandard\Sniffs\TestCase;
use Throwable;

class UselessIfConditionWithReturnSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/uselessIfConditionWithReturnNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/uselessIfConditionWithReturnErrors.php');

		self::assertSame(8, $report->getErrorCount());

		self::assertSniffError($report, 4, UselessIfConditionWithReturnSniff::CODE_USELESS_IF_CONDITION);
		self::assertSniffError($report, 12, UselessIfConditionWithReturnSniff::CODE_USELESS_IF_CONDITION);
		self::assertSniffError($report, 20, UselessIfConditionWithReturnSniff::CODE_USELESS_IF_CONDITION);
		self::assertSniffError($report, 28, UselessIfConditionWithReturnSniff::CODE_USELESS_IF_CONDITION);
		self::assertSniffError($report, 36, UselessIfConditionWithReturnSniff::CODE_USELESS_IF_CONDITION);
		self::assertSniffError($report, 44, UselessIfConditionWithReturnSniff::CODE_USELESS_IF_CONDITION);
		self::assertSniffError($report, 52, UselessIfConditionWithReturnSniff::CODE_USELESS_IF_CONDITION);
		self::assertSniffError($report, 61, UselessIfConditionWithReturnSniff::CODE_USELESS_IF_CONDITION);

		self::assertAllFixedInFile($report);
	}

	public function testErrorsWithAssumeAllConditionExpressionsAreAlreadyBooleanEnabled(): void
	{
		$report = self::checkFile(__DIR__ . '/data/uselessIfConditionWithReturnErrorsWithAssumeAllConditionExpressionsAreAlreadyBooleanEnabled.php', [
			'assumeAllConditionExpressionsAreAlreadyBoolean' => true,
		]);

		self::assertSame(2, $report->getErrorCount());

		self::assertSniffError($report, 4, UselessIfConditionWithReturnSniff::CODE_USELESS_IF_CONDITION);
		self::assertSniffError($report, 13, UselessIfConditionWithReturnSniff::CODE_USELESS_IF_CONDITION);

		self::assertAllFixedInFile($report);
	}

	public function testIfWithoutCurlyBraces(): void
	{
		self::expectException(Throwable::class);
		self::expectExceptionMessage('"if" without curly braces is not supported.');
		self::checkFile(__DIR__ . '/data/uselessIfConditionWithReturnIfWithoutCurlyBraces.php');
	}

	public function testElseWithoutCurlyBraces(): void
	{
		self::expectException(Throwable::class);
		self::expectExceptionMessage('"else" without curly braces is not supported.');
		self::checkFile(__DIR__ . '/data/uselessIfConditionWithReturnElseWithoutCurlyBraces.php');
	}

}
