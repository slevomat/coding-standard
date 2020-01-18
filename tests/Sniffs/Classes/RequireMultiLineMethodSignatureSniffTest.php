<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use SlevomatCodingStandard\Sniffs\TestCase;

final class RequireMultiLineMethodSignatureSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireMultiLineMethodSignatureNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireMultiLineMethodSignatureErrors.php');
		self::assertSame(4, $report->getErrorCount());

		self::assertSniffError($report, 5, RequireMultiLineMethodSignatureSniff::CODE_REQUIRED_MULTI_LINE_SIGNATURE);
		self::assertSniffError($report, 8, RequireMultiLineMethodSignatureSniff::CODE_REQUIRED_MULTI_LINE_SIGNATURE);
		self::assertSniffError($report, 14, RequireMultiLineMethodSignatureSniff::CODE_REQUIRED_MULTI_LINE_SIGNATURE);
		self::assertSniffError($report, 16, RequireMultiLineMethodSignatureSniff::CODE_REQUIRED_MULTI_LINE_SIGNATURE);

		self::assertAllFixedInFile($report);
	}

	public function testForAllMethods(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireMultiLineMethodSignatureAllMethodsErrors.php', ['minLineLength' => 0]);
		self::assertSame(1, $report->getErrorCount());

		self::assertSniffError($report, 5, RequireMultiLineMethodSignatureSniff::CODE_REQUIRED_MULTI_LINE_SIGNATURE);

		self::assertAllFixedInFile($report);
	}

}
