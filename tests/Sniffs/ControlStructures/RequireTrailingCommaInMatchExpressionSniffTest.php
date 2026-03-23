<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use SlevomatCodingStandard\Sniffs\TestCase;

class RequireTrailingCommaInMatchExpressionSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireTrailingCommaInMatchExpressionNoErrors.php', [
			'enable' => true,
		]);

		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireTrailingCommaInMatchExpressionErrors.php', [
			'enable' => true,
		]);

		self::assertSame(6, $report->getErrorCount());

		self::assertSniffError($report, 5, RequireTrailingCommaInMatchExpressionSniff::CODE_MISSING_TRAILING_COMMA);
		self::assertSniffError($report, 12, RequireTrailingCommaInMatchExpressionSniff::CODE_MISSING_TRAILING_COMMA);
		self::assertSniffError($report, 13, RequireTrailingCommaInMatchExpressionSniff::CODE_MISSING_TRAILING_COMMA);
		self::assertSniffError($report, 20, RequireTrailingCommaInMatchExpressionSniff::CODE_MISSING_TRAILING_COMMA);
		self::assertSniffError($report, 30, RequireTrailingCommaInMatchExpressionSniff::CODE_MISSING_TRAILING_COMMA);
		self::assertSniffError($report, 31, RequireTrailingCommaInMatchExpressionSniff::CODE_MISSING_TRAILING_COMMA);

		self::assertAllFixedInFile($report);
	}

	public function testShouldNotReportIfSniffIsDisabled(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireTrailingCommaInMatchExpressionErrors.php', [
			'enable' => false,
		]);

		self::assertNoSniffErrorInFile($report);
	}

}
