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
		$file = self::checkFile(__DIR__ . '/data/propertySpacingSniffErrors.php');

		self::assertSame(10, $file->getErrorCount());

		self::assertSniffError($file, 5, PropertySpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_PROPERTY);
		self::assertSniffError($file, 7, PropertySpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_PROPERTY);
		self::assertSniffError($file, 9, PropertySpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_PROPERTY);
		self::assertSniffError($file, 11, PropertySpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_PROPERTY);
		self::assertSniffError($file, 13, PropertySpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_PROPERTY);
		self::assertSniffError($file, 30, PropertySpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_PROPERTY);
		self::assertSniffError($file, 34, PropertySpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_PROPERTY);
		self::assertSniffError($file, 39, PropertySpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_PROPERTY);
		self::assertSniffError($file, 43, PropertySpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_PROPERTY);
		self::assertSniffError($file, 49, PropertySpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_PROPERTY);

		self::assertAllFixedInFile($file);
	}

}
