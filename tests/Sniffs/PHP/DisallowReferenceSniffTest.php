<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\PHP;

use SlevomatCodingStandard\Sniffs\TestCase;

class DisallowReferenceSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowReferenceNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowReferenceErrors.php');

		self::assertSame(12, $report->getErrorCount());

		self::assertSniffError($report, 3, DisallowReferenceSniff::CODE_DISALLOWED_RETURNING_REFERENCE);
		self::assertSniffError($report, 8, DisallowReferenceSniff::CODE_DISALLOWED_RETURNING_REFERENCE);
		self::assertSniffError($report, 13, DisallowReferenceSniff::CODE_DISALLOWED_PASSING_BY_REFERENCE);
		self::assertSniffError($report, 17, DisallowReferenceSniff::CODE_DISALLOWED_PASSING_BY_REFERENCE);
		self::assertSniffError($report, 22, DisallowReferenceSniff::CODE_DISALLOWED_INHERITING_VARIABLE_BY_REFERENCE);
		self::assertSniffError($report, 26, DisallowReferenceSniff::CODE_DISALLOWED_ASSIGNING_BY_REFERENCE);
		self::assertSniffError($report, 28, DisallowReferenceSniff::CODE_DISALLOWED_ASSIGNING_BY_REFERENCE);
		self::assertSniffError($report, 29, DisallowReferenceSniff::CODE_DISALLOWED_ASSIGNING_BY_REFERENCE);
		self::assertSniffError($report, 30, DisallowReferenceSniff::CODE_DISALLOWED_ASSIGNING_BY_REFERENCE);
		self::assertSniffError($report, 33, DisallowReferenceSniff::CODE_DISALLOWED_ASSIGNING_BY_REFERENCE);
		self::assertSniffError($report, 37, DisallowReferenceSniff::CODE_DISALLOWED_RETURNING_REFERENCE);
		self::assertSniffError($report, 37, DisallowReferenceSniff::CODE_DISALLOWED_PASSING_BY_REFERENCE);
	}

}
