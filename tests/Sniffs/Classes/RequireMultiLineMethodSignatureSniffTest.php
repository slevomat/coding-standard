<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use SlevomatCodingStandard\Sniffs\TestCase;
use Throwable;

final class RequireMultiLineMethodSignatureSniffTest extends TestCase
{

	public function testThrowExceptionForInvalidPattern(): void
	{
		$this->expectException(Throwable::class);

		self::checkFile(
			__DIR__ . '/data/requireMultiLineMethodSignatureNoErrors.php',
			['includedMethodPatterns' => ['invalidPattern']]
		);

		self::checkFile(
			__DIR__ . '/data/requireMultiLineMethodSignatureNoErrors.php',
			['excludedMethodPatterns' => ['invalidPattern']]
		);
	}

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

	public function testIncludedMethodPatterns(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireMultiLineMethodSignatureIncludedMethodsErrors.php', [
			'maxLineLength' => 0,
			'includedMethodPatterns' => ['/__construct/'],
		], [RequireMultiLineMethodSignatureSniff::CODE_REQUIRED_MULTI_LINE_SIGNATURE]);

		self::assertSame(1, $report->getErrorCount());

		self::assertSniffError($report, 5, RequireMultiLineMethodSignatureSniff::CODE_REQUIRED_MULTI_LINE_SIGNATURE);

		self::assertAllFixedInFile($report);
	}

	public function testExcludedMethodPatterns(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireMultiLineMethodSignatureExcludedMethodsErrors.php', [
			'maxLineLength' => 0,
			'excludedMethodPatterns' => ['/__construct/'],
		], [RequireMultiLineMethodSignatureSniff::CODE_REQUIRED_MULTI_LINE_SIGNATURE]);

		self::assertSame(1, $report->getErrorCount());

		self::assertSniffError($report, 7, RequireMultiLineMethodSignatureSniff::CODE_REQUIRED_MULTI_LINE_SIGNATURE);

		self::assertAllFixedInFile($report);
	}

}
