<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Exceptions;

use SlevomatCodingStandard\Sniffs\TestCase;

class DisallowNonCapturingCatchSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowNonCapturingCatchNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowNonCapturingCatchErrors.php');

		self::assertSame(3, $report->getErrorCount());

		self::assertSniffError($report, 5, DisallowNonCapturingCatchSniff::CODE_DISALLOWED_NON_CAPTURING_CATCH);
		self::assertSniffError($report, 10, DisallowNonCapturingCatchSniff::CODE_DISALLOWED_NON_CAPTURING_CATCH);
		self::assertSniffError($report, 16, DisallowNonCapturingCatchSniff::CODE_DISALLOWED_NON_CAPTURING_CATCH);
	}

}
