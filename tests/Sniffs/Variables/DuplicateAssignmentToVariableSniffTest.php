<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Variables;

use SlevomatCodingStandard\Sniffs\TestCase;

class DuplicateAssignmentToVariableSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/duplicateAssignmentToVariableNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/duplicateAssignmentToVariableErrors.php');

		self::assertSame(2, $report->getErrorCount());

		self::assertSniffError(
			$report,
			3,
			DuplicateAssignmentToVariableSniff::CODE_DUPLICATE_ASSIGNMENT,
			'Duplicate assignment to variable $a.'
		);
	}

}
