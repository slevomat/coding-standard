<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use SlevomatCodingStandard\Sniffs\TestCase;

final class RequireSingleLineMethodSignatureSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireSingleLineMethodSignatureNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireSingleLineMethodSignatureErrors.php');
		self::assertSame(4, $report->getErrorCount());

		self::assertSniffError($report, 5, RequireSingleLineMethodSignatureSniff::CODE_REQUIRED_SINGLE_LINE_SIGNATURE);
		self::assertSniffError($report, 11, RequireSingleLineMethodSignatureSniff::CODE_REQUIRED_SINGLE_LINE_SIGNATURE);
		self::assertSniffError($report, 20, RequireSingleLineMethodSignatureSniff::CODE_REQUIRED_SINGLE_LINE_SIGNATURE);
		self::assertSniffError($report, 25, RequireSingleLineMethodSignatureSniff::CODE_REQUIRED_SINGLE_LINE_SIGNATURE);

		self::assertAllFixedInFile($report);
	}

	public function testForAllMethods(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireSingleLineMethodSignatureAllMethodsErrors.php', ['maxLineLength' => 0]);
		self::assertSame(1, $report->getErrorCount());

		self::assertSniffError($report, 5, RequireSingleLineMethodSignatureSniff::CODE_REQUIRED_SINGLE_LINE_SIGNATURE);

		self::assertAllFixedInFile($report);
	}

}
