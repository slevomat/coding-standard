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

		self::assertSame(28, $report->getErrorCount());

		self::assertSniffError($report, 3, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 6, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 9, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 12, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 15, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 23, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 26, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 32, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 35, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 38, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 41, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 44, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 47, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 51, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 56, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 57, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 62, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 66, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 67, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 74, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 78, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 82, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 88, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 89, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 96, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 97, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 102, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);
		self::assertSniffError($report, 109, RequireExplicitAssertionSniff::CODE_REQUIRED_EXPLICIT_ASSERTION);

		self::assertAllFixedInFile($report);
	}

}
