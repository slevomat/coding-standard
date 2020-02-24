<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Variables;

use SlevomatCodingStandard\Sniffs\TestCase;

class DisallowSuperGlobalVariableSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/duplicateAssignmentToVariableNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowSuperGlobalVariableErrors.php');

		self::assertSame(9, $report->getErrorCount());

		self::assertSniffError($report, 3, DisallowSuperGlobalVariableSniff::CODE_DISALLOWED_SUPER_GLOBAL_VARIABLE);
		self::assertSniffError($report, 4, DisallowSuperGlobalVariableSniff::CODE_DISALLOWED_SUPER_GLOBAL_VARIABLE);
		self::assertSniffError($report, 5, DisallowSuperGlobalVariableSniff::CODE_DISALLOWED_SUPER_GLOBAL_VARIABLE);
		self::assertSniffError($report, 6, DisallowSuperGlobalVariableSniff::CODE_DISALLOWED_SUPER_GLOBAL_VARIABLE);
		self::assertSniffError($report, 7, DisallowSuperGlobalVariableSniff::CODE_DISALLOWED_SUPER_GLOBAL_VARIABLE);
		self::assertSniffError($report, 8, DisallowSuperGlobalVariableSniff::CODE_DISALLOWED_SUPER_GLOBAL_VARIABLE);
		self::assertSniffError($report, 9, DisallowSuperGlobalVariableSniff::CODE_DISALLOWED_SUPER_GLOBAL_VARIABLE);
		self::assertSniffError($report, 10, DisallowSuperGlobalVariableSniff::CODE_DISALLOWED_SUPER_GLOBAL_VARIABLE);
		self::assertSniffError($report, 11, DisallowSuperGlobalVariableSniff::CODE_DISALLOWED_SUPER_GLOBAL_VARIABLE);
	}

}
