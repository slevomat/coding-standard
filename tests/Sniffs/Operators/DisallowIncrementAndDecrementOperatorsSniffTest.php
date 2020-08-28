<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Operators;

use SlevomatCodingStandard\Sniffs\TestCase;

class DisallowIncrementAndDecrementOperatorsSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowIncrementAndDecrementOperatorsNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowIncrementAndDecrementOperatorsErrors.php');

		self::assertSame(6, $report->getErrorCount());

		self::assertSniffError($report, 3, DisallowIncrementAndDecrementOperatorsSniff::CODE_DISALLOWED_POST_INCREMENT_OPERATOR);
		self::assertSniffError($report, 4, DisallowIncrementAndDecrementOperatorsSniff::CODE_DISALLOWED_PRE_INCREMENT_OPERATOR);
		self::assertSniffError($report, 6, DisallowIncrementAndDecrementOperatorsSniff::CODE_DISALLOWED_POST_DECREMENT_OPERATOR);
		self::assertSniffError($report, 7, DisallowIncrementAndDecrementOperatorsSniff::CODE_DISALLOWED_PRE_DECREMENT_OPERATOR);
		self::assertSniffError($report, 9, DisallowIncrementAndDecrementOperatorsSniff::CODE_DISALLOWED_POST_INCREMENT_OPERATOR);
		self::assertSniffError($report, 10, DisallowIncrementAndDecrementOperatorsSniff::CODE_DISALLOWED_POST_DECREMENT_OPERATOR);
	}

}
