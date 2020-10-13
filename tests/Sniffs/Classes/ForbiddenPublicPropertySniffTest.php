<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use SlevomatCodingStandard\Sniffs\TestCase;

class ForbiddenPublicPropertySniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/forbiddenPublicPropertyNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/forbiddenPublicPropertyErrors.php');

		self::assertSame(2, $report->getErrorCount());

		self::assertSniffError($report, 5, ForbiddenPublicPropertySniff::CODE_FORBIDDEN_PUBLIC_PROPERTY);
		self::assertSniffError($report, 6, ForbiddenPublicPropertySniff::CODE_FORBIDDEN_PUBLIC_PROPERTY);
	}

}
