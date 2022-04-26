<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Functions;

use SlevomatCodingStandard\Sniffs\TestCase;

class DisallowTrailingCommaInDeclarationSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowTrailingCommaInDeclarationNoErrors.php', [
			'onlySingleLine' => false,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowTrailingCommaInDeclarationErrors.php', [
			'onlySingleLine' => false,
		]);

		self::assertSame(4, $report->getErrorCount());

		self::assertSniffError($report, 5, DisallowTrailingCommaInDeclarationSniff::CODE_DISALLOWED_TRAILING_COMMA);
		self::assertSniffError($report, 14, DisallowTrailingCommaInDeclarationSniff::CODE_DISALLOWED_TRAILING_COMMA);
		self::assertSniffError($report, 24, DisallowTrailingCommaInDeclarationSniff::CODE_DISALLOWED_TRAILING_COMMA);
		self::assertSniffError($report, 28, DisallowTrailingCommaInDeclarationSniff::CODE_DISALLOWED_TRAILING_COMMA);

		self::assertAllFixedInFile($report);
	}

	public function testWithOnlySingleLineEnabledErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowTrailingCommaInDeclarationWithOnlySingleLineEnabledErrors.php', [
			'onlySingleLine' => true,
		]);

		self::assertSame(1, $report->getErrorCount());

		self::assertSniffError($report, 10, DisallowTrailingCommaInDeclarationSniff::CODE_DISALLOWED_TRAILING_COMMA);

		self::assertAllFixedInFile($report);
	}

}
