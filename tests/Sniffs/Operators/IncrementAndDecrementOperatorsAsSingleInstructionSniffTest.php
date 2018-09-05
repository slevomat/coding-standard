<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Operators;

use SlevomatCodingStandard\Sniffs\TestCase;

class IncrementAndDecrementOperatorsAsSingleInstructionSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/incrementAndDecrementOperatorsAsSingleInstructionNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/incrementAndDecrementOperatorsAsSingleInstructionErrors.php');

		self::assertSame(5, $report->getErrorCount());

		self::assertSniffError($report, 3, IncrementAndDecrementOperatorsAsSingleInstructionSniff::CODE_POST_INCREMENT_OPERATOR_NOT_USED_AS_SINGLE_INSTRUCTION);
		self::assertSniffError($report, 4, IncrementAndDecrementOperatorsAsSingleInstructionSniff::CODE_PRE_INCREMENT_OPERATOR_NOT_USED_AS_SINGLE_INSTRUCTION);
		self::assertSniffError($report, 7, IncrementAndDecrementOperatorsAsSingleInstructionSniff::CODE_POST_DECREMENT_OPERATOR_NOT_USED_AS_SINGLE_INSTRUCTION);
		self::assertSniffError($report, 7, IncrementAndDecrementOperatorsAsSingleInstructionSniff::CODE_PRE_DECREMENT_OPERATOR_NOT_USED_AS_SINGLE_INSTRUCTION);
		self::assertSniffError($report, 10, IncrementAndDecrementOperatorsAsSingleInstructionSniff::CODE_POST_INCREMENT_OPERATOR_NOT_USED_AS_SINGLE_INSTRUCTION);
	}

}
