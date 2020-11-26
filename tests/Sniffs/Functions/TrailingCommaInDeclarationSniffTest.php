<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Functions;

use SlevomatCodingStandard\Sniffs\TestCase;

class TrailingCommaInDeclarationSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/trailingCommaInDeclarationNoErrors.php', [
			'enable' => true,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/trailingCommaInDeclarationErrors.php', [
			'enable' => true,
		]);

		self::assertSame(4, $report->getErrorCount());

		self::assertSniffError($report, 5, TrailingCommaInDeclarationSniff::CODE_MISSING_TRAILING_COMMA);
		self::assertSniffError($report, 14, TrailingCommaInDeclarationSniff::CODE_MISSING_TRAILING_COMMA);
		self::assertSniffError($report, 24, TrailingCommaInDeclarationSniff::CODE_MISSING_TRAILING_COMMA);
		self::assertSniffError($report, 28, TrailingCommaInDeclarationSniff::CODE_MISSING_TRAILING_COMMA);

		self::assertAllFixedInFile($report);
	}

	public function testShouldNotReportIfSniffIsDisabled(): void
	{
		$report = self::checkFile(__DIR__ . '/data/trailingCommaInDeclarationErrors.php', [
			'enable' => false,
		]);

		self::assertNoSniffErrorInFile($report);
	}

}
