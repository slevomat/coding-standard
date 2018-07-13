<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use SlevomatCodingStandard\Sniffs\TestCase;

class SuperfluousExceptionNamingSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/superfluousExceptionNamingNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/superfluousExceptionNamingErrors.php');

		self::assertSame(3, $report->getErrorCount());

		self::assertSniffError($report, 3, SuperfluousExceptionNamingSniff::CODE_SUPERFLUOUS_SUFFIX, 'Superfluous suffix "Exception".');
		self::assertSniffError($report, 8, SuperfluousExceptionNamingSniff::CODE_SUPERFLUOUS_SUFFIX, 'Superfluous suffix "exception".');
		self::assertSniffError($report, 13, SuperfluousExceptionNamingSniff::CODE_SUPERFLUOUS_SUFFIX, 'Superfluous suffix "ExCePtIoN".');
	}

}
