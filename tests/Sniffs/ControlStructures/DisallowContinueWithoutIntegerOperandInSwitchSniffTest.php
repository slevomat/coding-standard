<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use SlevomatCodingStandard\Sniffs\TestCase;

class DisallowContinueWithoutIntegerOperandInSwitchSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowContinueWithoutIntegerOperandInSwitchNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowContinueWithoutIntegerOperandInSwitchErrors.php');

		self::assertSame(2, $report->getErrorCount());

		self::assertSniffError(
			$report,
			6,
			DisallowContinueWithoutIntegerOperandInSwitchSniff::CODE_DISALLOWED_CONTINUE_WITHOUT_INTEGER_OPERAND_IN_SWITCH
		);
		self::assertSniffError(
			$report,
			12,
			DisallowContinueWithoutIntegerOperandInSwitchSniff::CODE_DISALLOWED_CONTINUE_WITHOUT_INTEGER_OPERAND_IN_SWITCH
		);

		self::assertAllFixedInFile($report);
	}

}
