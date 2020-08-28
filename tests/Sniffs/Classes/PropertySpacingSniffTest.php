<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use SlevomatCodingStandard\Sniffs\TestCase;

class PropertySpacingSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/propertySpacingNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/propertySpacingErrors.php');

		self::assertSame(13, $report->getErrorCount());

		self::assertSniffError($report, 5, PropertySpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_PROPERTY);
		self::assertSniffError($report, 7, PropertySpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_PROPERTY);
		self::assertSniffError($report, 9, PropertySpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_PROPERTY);
		self::assertSniffError($report, 11, PropertySpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_PROPERTY);
		self::assertSniffError($report, 13, PropertySpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_PROPERTY);
		self::assertSniffError($report, 30, PropertySpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_PROPERTY);
		self::assertSniffError($report, 34, PropertySpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_PROPERTY);
		self::assertSniffError($report, 39, PropertySpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_PROPERTY);
		self::assertSniffError($report, 43, PropertySpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_PROPERTY);
		self::assertSniffError($report, 49, PropertySpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_PROPERTY);
		self::assertSniffError($report, 53, PropertySpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_PROPERTY);
		self::assertSniffError($report, 56, PropertySpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_PROPERTY);
		self::assertSniffError($report, 59, PropertySpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_PROPERTY);

		self::assertAllFixedInFile($report);
	}

	public function testZeroBlankLinesErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/propertySpacingZeroBlankLinesErrors.php', [
			'minLinesCountBeforeWithComment' => 0,
			'maxLinesCountBeforeWithComment' => 0,
			'minLinesCountBeforeWithoutComment' => 0,
			'maxLinesCountBeforeWithoutComment' => 0,
		]);

		self::assertSame(3, $report->getErrorCount());

		self::assertSniffError($report, 5, PropertySpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_PROPERTY);
		self::assertSniffError($report, 7, PropertySpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_PROPERTY);
		self::assertSniffError($report, 10, PropertySpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_PROPERTY);

		self::assertAllFixedInFile($report);
	}

}
