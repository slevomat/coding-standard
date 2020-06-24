<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use SlevomatCodingStandard\Sniffs\TestCase;

class SuperfluousErrorNamingSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/superfluousErrorNamingNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/superfluousErrorNamingErrors.php');

		self::assertSame(3, $report->getErrorCount());

		self::assertSniffError($report, 3, SuperfluousExceptionNamingSniff::CODE_SUPERFLUOUS_SUFFIX, 'Superfluous suffix "Error".');
		self::assertSniffError($report, 8, SuperfluousExceptionNamingSniff::CODE_SUPERFLUOUS_SUFFIX, 'Superfluous suffix "error".');
		self::assertSniffError($report, 13, SuperfluousExceptionNamingSniff::CODE_SUPERFLUOUS_SUFFIX, 'Superfluous suffix "ErrOR".');
	}

}
