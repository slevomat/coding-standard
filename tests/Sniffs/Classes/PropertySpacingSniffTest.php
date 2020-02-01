<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use SlevomatCodingStandard\Sniffs\TestCase;

class PropertySpacingSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		self::assertNoSniffErrorInFile(self::checkFile(__DIR__ . '/data/propertySpacingSniffNoErrors.php'));
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/propertySpacingSniffErrors.php');

		self::assertSame(11, $report->getErrorCount());

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

		self::assertAllFixedInFile($report);
	}

}
