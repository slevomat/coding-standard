<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\PHP;

use SlevomatCodingStandard\Sniffs\TestCase;

class RequireExplicitAssertionSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireExplicitAssertionNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireExplicitAssertionErrors.php');

		self::assertSame(29, $report->getErrorCount());

		self::assertSniffError($report, 3, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 6, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 9, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 12, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 15, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 23, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 26, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 32, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 38, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 41, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 44, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 47, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 50, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 53, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 57, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 62, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 63, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 68, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 72, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 73, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 80, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 84, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 88, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 94, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 95, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 102, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 103, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 108, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 115, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);

		self::assertAllFixedInFile($report);
	}

}
