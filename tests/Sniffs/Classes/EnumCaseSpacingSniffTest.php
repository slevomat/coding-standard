<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use SlevomatCodingStandard\Sniffs\TestCase;

class EnumCaseSpacingSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/enumCaseSpacingNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/enumCaseSpacingErrors.php');

		self::assertSame(4, $report->getErrorCount());

		self::assertSniffError($report, 5, EnumCaseSpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_ENUM_CASE);
		self::assertSniffError($report, 24, EnumCaseSpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_ENUM_CASE);
		self::assertSniffError($report, 28, EnumCaseSpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_ENUM_CASE);
		self::assertSniffError($report, 55, EnumCaseSpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_ENUM_CASE);

		self::assertAllFixedInFile($report);
	}

	public function testErrorsWithModifiedLinesCount(): void
	{
		$report = self::checkFile(__DIR__ . '/data/enumCaseSpacingErrors.php', [
			'minLinesCountBeforeWithComment' => 2,
			'maxLinesCountBeforeWithComment' => 2,
			'maxLinesCountBeforeWithoutComment' => 2,
		]);

		self::assertSame(2, $report->getErrorCount());

		self::assertSniffError($report, 5, EnumCaseSpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_ENUM_CASE);
		self::assertSniffError($report, 28, EnumCaseSpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_ENUM_CASE);
	}

}
