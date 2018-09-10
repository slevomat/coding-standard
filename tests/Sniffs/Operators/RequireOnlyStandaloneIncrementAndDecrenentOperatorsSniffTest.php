<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Operators;

use SlevomatCodingStandard\Sniffs\TestCase;

class RequireOnlyStandaloneIncrementAndDecrenentOperatorsSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireOnlyStandaloneIncrementAndDecrenentOperatorsNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireOnlyStandaloneIncrementAndDecrenentOperatorsErrors.php');

		self::assertSame(5, $report->getErrorCount());

		self::assertSniffError($report, 3, RequireOnlyStandaloneIncrementAndDecrenentOperatorsSniff::CODE_POST_INCREMENT_OPERATOR_NOT_USED_STANDALONE);
		self::assertSniffError($report, 4, RequireOnlyStandaloneIncrementAndDecrenentOperatorsSniff::CODE_PRE_INCREMENT_OPERATOR_NOT_USED_STANDALONE);
		self::assertSniffError($report, 7, RequireOnlyStandaloneIncrementAndDecrenentOperatorsSniff::CODE_POST_DECREMENT_OPERATOR_NOT_USED_STANDALONE);
		self::assertSniffError($report, 7, RequireOnlyStandaloneIncrementAndDecrenentOperatorsSniff::CODE_PRE_DECREMENT_OPERATOR_NOT_USED_STANDALONE);
		self::assertSniffError($report, 10, RequireOnlyStandaloneIncrementAndDecrenentOperatorsSniff::CODE_POST_INCREMENT_OPERATOR_NOT_USED_STANDALONE);
	}

}
