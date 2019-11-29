<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Functions;

use SlevomatCodingStandard\Sniffs\TestCase;

class UnusedParameterSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/unusedParameterNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/unusedParameterErrors.php');

		self::assertSame(11, $report->getErrorCount());

		self::assertSniffError($report, 3, UnusedParameterSniff::CODE_UNUSED_PARAMETER, 'Unused parameter $a.');
		self::assertSniffError($report, 3, UnusedParameterSniff::CODE_UNUSED_PARAMETER, 'Unused parameter $b.');
		self::assertSniffError($report, 7, UnusedParameterSniff::CODE_UNUSED_PARAMETER, 'Unused parameter $a.');
		self::assertSniffError($report, 14, UnusedParameterSniff::CODE_UNUSED_PARAMETER, 'Unused parameter $a.');
		self::assertSniffError($report, 19, UnusedParameterSniff::CODE_UNUSED_PARAMETER, 'Unused parameter $a.');
		self::assertSniffError($report, 26, UnusedParameterSniff::CODE_UNUSED_PARAMETER, 'Unused parameter $a.');
		self::assertSniffError($report, 35, UnusedParameterSniff::CODE_UNUSED_PARAMETER, 'Unused parameter $a.');
		self::assertSniffError($report, 40, UnusedParameterSniff::CODE_UNUSED_PARAMETER, 'Unused parameter $a.');
		self::assertSniffError($report, 47, UnusedParameterSniff::CODE_UNUSED_PARAMETER, 'Unused parameter $a.');
		self::assertSniffError($report, 51, UnusedParameterSniff::CODE_UNUSED_PARAMETER, 'Unused parameter $a.');
		self::assertSniffError($report, 53, UnusedParameterSniff::CODE_UNUSED_PARAMETER, 'Unused parameter $a.');
	}

}
