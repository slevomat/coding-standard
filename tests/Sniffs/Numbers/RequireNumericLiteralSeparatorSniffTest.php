<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Numbers;

use SlevomatCodingStandard\Sniffs\TestCase;

class RequireNumericLiteralSeparatorSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireNumericLiteralSeparatorNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireNumericLiteralSeparatorErrors.php');

		self::assertSame(5, $report->getErrorCount());

		self::assertSniffError($report, 3, RequireNumericLiteralSeparatorSniff::CODE_REQUIRED_NUMERIC_LITERAL_SEPARATOR);
		self::assertSniffError($report, 4, RequireNumericLiteralSeparatorSniff::CODE_REQUIRED_NUMERIC_LITERAL_SEPARATOR);
		self::assertSniffError($report, 5, RequireNumericLiteralSeparatorSniff::CODE_REQUIRED_NUMERIC_LITERAL_SEPARATOR);
		self::assertSniffError($report, 6, RequireNumericLiteralSeparatorSniff::CODE_REQUIRED_NUMERIC_LITERAL_SEPARATOR);
		self::assertSniffError($report, 7, RequireNumericLiteralSeparatorSniff::CODE_REQUIRED_NUMERIC_LITERAL_SEPARATOR);
	}

}
