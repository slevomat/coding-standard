<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Numbers;

use SlevomatCodingStandard\Sniffs\TestCase;

class RequireNumericLiteralSeparatorSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireNumericLiteralSeparatorNoErrors.php', [
			'enable' => true,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireNumericLiteralSeparatorErrors.php', [
			'enable' => true,
		]);

		self::assertSame(5, $report->getErrorCount());

		self::assertSniffError($report, 3, RequireNumericLiteralSeparatorSniff::CODE_REQUIRED_NUMERIC_LITERAL_SEPARATOR);
		self::assertSniffError($report, 4, RequireNumericLiteralSeparatorSniff::CODE_REQUIRED_NUMERIC_LITERAL_SEPARATOR);
		self::assertSniffError($report, 5, RequireNumericLiteralSeparatorSniff::CODE_REQUIRED_NUMERIC_LITERAL_SEPARATOR);
		self::assertSniffError($report, 6, RequireNumericLiteralSeparatorSniff::CODE_REQUIRED_NUMERIC_LITERAL_SEPARATOR);
		self::assertSniffError($report, 7, RequireNumericLiteralSeparatorSniff::CODE_REQUIRED_NUMERIC_LITERAL_SEPARATOR);
	}

	public function testModifiedSettingsNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireNumericLiteralSeparatorModifiedSettingsNoErrors.php', [
			'enable' => true,
			'minDigitsBeforeDecimalPoint' => 7,
			'minDigitsAfterDecimalPoint' => 6,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testModifiedSettingsErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireNumericLiteralSeparatorModifiedSettingsErrors.php', [
			'enable' => true,
			'minDigitsBeforeDecimalPoint' => 7,
			'minDigitsAfterDecimalPoint' => 6,
		]);

		self::assertSame(2, $report->getErrorCount());

		self::assertSniffError($report, 3, RequireNumericLiteralSeparatorSniff::CODE_REQUIRED_NUMERIC_LITERAL_SEPARATOR);
		self::assertSniffError($report, 4, RequireNumericLiteralSeparatorSniff::CODE_REQUIRED_NUMERIC_LITERAL_SEPARATOR);
	}

	public function testShouldNotReportIfSniffIsDisabled(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireNumericLiteralSeparatorErrors.php', [
			'enable' => false,
		]);

		self::assertSame(0, $report->getErrorCount());
		self::assertNoSniffErrorInFile($report);
	}

}
