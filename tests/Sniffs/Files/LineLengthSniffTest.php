<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Files;

use SlevomatCodingStandard\Sniffs\TestCase;

class LineLengthSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/lineLengthNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/lineLengthErrors.php', ['ignoreImports' => false]);

		self::assertSame(7, $report->getErrorCount());

		self::assertSniffError($report, 5, LineLengthSniff::CODE_LINE_TOO_LONG);
		self::assertSniffError($report, 7, LineLengthSniff::CODE_LINE_TOO_LONG);
		self::assertSniffError($report, 10, LineLengthSniff::CODE_LINE_TOO_LONG);
		self::assertSniffError($report, 12, LineLengthSniff::CODE_LINE_TOO_LONG);
		self::assertSniffError($report, 15, LineLengthSniff::CODE_LINE_TOO_LONG);
		self::assertSniffError($report, 19, LineLengthSniff::CODE_LINE_TOO_LONG);
		self::assertSniffError($report, 20, LineLengthSniff::CODE_LINE_TOO_LONG);
	}

	public function testIgnoreCommentsErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/lineLengthIgnoreCommentsErrors.php', ['ignoreComments' => true]);

		self::assertSame(4, $report->getErrorCount());

		self::assertSniffError($report, 7, LineLengthSniff::CODE_LINE_TOO_LONG);
		self::assertSniffError($report, 12, LineLengthSniff::CODE_LINE_TOO_LONG);
		self::assertSniffError($report, 15, LineLengthSniff::CODE_LINE_TOO_LONG);
		self::assertSniffError($report, 20, LineLengthSniff::CODE_LINE_TOO_LONG);
	}

	public function testIgnoreImportsErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/lineLengthIgnoreImportsErrors.php');

		self::assertSame(7, $report->getErrorCount());

		self::assertSniffError($report, 7, LineLengthSniff::CODE_LINE_TOO_LONG);
		self::assertSniffError($report, 9, LineLengthSniff::CODE_LINE_TOO_LONG);
		self::assertSniffError($report, 12, LineLengthSniff::CODE_LINE_TOO_LONG);
		self::assertSniffError($report, 14, LineLengthSniff::CODE_LINE_TOO_LONG);
		self::assertSniffError($report, 17, LineLengthSniff::CODE_LINE_TOO_LONG);
		self::assertSniffError($report, 21, LineLengthSniff::CODE_LINE_TOO_LONG);
		self::assertSniffError($report, 22, LineLengthSniff::CODE_LINE_TOO_LONG);
	}

	public function testWithoutUseStatementsErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/lineLengthWithoutUseStatementsErrors.php');

		self::assertSame(6, $report->getErrorCount());

		self::assertSniffError($report, 5, LineLengthSniff::CODE_LINE_TOO_LONG);
		self::assertSniffError($report, 8, LineLengthSniff::CODE_LINE_TOO_LONG);
		self::assertSniffError($report, 10, LineLengthSniff::CODE_LINE_TOO_LONG);
		self::assertSniffError($report, 13, LineLengthSniff::CODE_LINE_TOO_LONG);
		self::assertSniffError($report, 17, LineLengthSniff::CODE_LINE_TOO_LONG);
		self::assertSniffError($report, 18, LineLengthSniff::CODE_LINE_TOO_LONG);
	}

}
