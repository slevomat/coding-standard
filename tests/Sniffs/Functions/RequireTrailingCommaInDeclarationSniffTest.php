<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Functions;

use SlevomatCodingStandard\Sniffs\TestCase;

class RequireTrailingCommaInDeclarationSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireTrailingCommaInDeclarationNoErrors.php', [
			'enable' => true,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireTrailingCommaInDeclarationErrors.php', [
			'enable' => true,
		]);

		self::assertSame(4, $report->getErrorCount());

		self::assertSniffError($report, 5, RequireTrailingCommaInDeclarationSniff::CODE_MISSING_TRAILING_COMMA);
		self::assertSniffError($report, 14, RequireTrailingCommaInDeclarationSniff::CODE_MISSING_TRAILING_COMMA);
		self::assertSniffError($report, 24, RequireTrailingCommaInDeclarationSniff::CODE_MISSING_TRAILING_COMMA);
		self::assertSniffError($report, 28, RequireTrailingCommaInDeclarationSniff::CODE_MISSING_TRAILING_COMMA);

		self::assertAllFixedInFile($report);
	}

	public function testShouldNotReportIfSniffIsDisabled(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireTrailingCommaInDeclarationErrors.php', [
			'enable' => false,
		]);

		self::assertNoSniffErrorInFile($report);
	}

}
