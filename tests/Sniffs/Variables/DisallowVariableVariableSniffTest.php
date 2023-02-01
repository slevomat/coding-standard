<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Variables;

use SlevomatCodingStandard\Sniffs\TestCase;

class DisallowVariableVariableSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowVariableVariableNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowVariableVariableErrors.php');

		self::assertSame(3, $report->getErrorCount());

		self::assertSniffError($report, 4, DisallowVariableVariableSniff::CODE_DISALLOWED_VARIABLE_VARIABLE);
		self::assertSniffError($report, 13, DisallowVariableVariableSniff::CODE_DISALLOWED_VARIABLE_VARIABLE);
		self::assertSniffError($report, 14, DisallowVariableVariableSniff::CODE_DISALLOWED_VARIABLE_VARIABLE);
	}

}
