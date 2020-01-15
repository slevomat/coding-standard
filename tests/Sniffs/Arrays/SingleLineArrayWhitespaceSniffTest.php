<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Arrays;

use SlevomatCodingStandard\Sniffs\TestCase;

class SingleLineArrayWhitespaceSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		self::assertNoSniffErrorInFile(self::checkFile(__DIR__ . '/data/singleLineArrayWhitespaceNoErrors.php'));
	}

	public function testErrors(): void
	{
		$file = self::checkFile(__DIR__ . '/data/singleLineArrayWhitespaceErrors.php');

		self::assertSame(15, $file->getErrorCount());

		self::assertSniffError($file, 3, SingleLineArrayWhitespaceSniff::CODE_SPACE_BEFORE_COMMA, '1 found');
		self::assertSniffError($file, 3, SingleLineArrayWhitespaceSniff::CODE_SPACE_AFTER_COMMA, '2 found');
		self::assertSniffError($file, 3, SingleLineArrayWhitespaceSniff::CODE_SPACE_AFTER_COMMA, '0 found');
		self::assertSniffError($file, 3, SingleLineArrayWhitespaceSniff::CODE_SPACE_BEFORE_COMMA, '5 found');
		self::assertSniffError($file, 3, SingleLineArrayWhitespaceSniff::CODE_SPACE_AFTER_COMMA, '5 found');

		self::assertSniffError($file, 5, SingleLineArrayWhitespaceSniff::CODE_SPACE_AFTER_ARRAY_OPEN, '1 found');
		self::assertSniffError($file, 5, SingleLineArrayWhitespaceSniff::CODE_SPACE_BEFORE_ARRAY_CLOSE, '1 found');
		self::assertSniffError($file, 6, SingleLineArrayWhitespaceSniff::CODE_SPACE_AFTER_ARRAY_OPEN, '1 found');
		self::assertSniffError($file, 6, SingleLineArrayWhitespaceSniff::CODE_SPACE_BEFORE_ARRAY_CLOSE, '1 found');
		self::assertSniffError($file, 7, SingleLineArrayWhitespaceSniff::CODE_SPACE_AFTER_ARRAY_OPEN, '1 found');
		self::assertSniffError($file, 7, SingleLineArrayWhitespaceSniff::CODE_SPACE_BEFORE_ARRAY_CLOSE, '1 found');
		self::assertSniffError($file, 8, SingleLineArrayWhitespaceSniff::CODE_SPACE_AFTER_ARRAY_OPEN, '1 found');
		self::assertSniffError($file, 8, SingleLineArrayWhitespaceSniff::CODE_SPACE_BEFORE_ARRAY_CLOSE, '1 found');
		self::assertCount(4, $file->getErrors()[8]);

		self::assertAllFixedInFile($file);
	}

	public function testRequireSpacesErrors(): void
	{
		$file = self::checkFile(__DIR__ . '/data/singleLineArrayWhitespaceRequireSpacesErrors.php', ['spacesAroundBrackets' => 1]);

		self::assertSame(4, $file->getErrorCount());

		self::assertSniffError($file, 3, SingleLineArrayWhitespaceSniff::CODE_SPACE_AFTER_ARRAY_OPEN, '0 found');
		self::assertSniffError($file, 3, SingleLineArrayWhitespaceSniff::CODE_SPACE_BEFORE_ARRAY_CLOSE, '0 found');

		self::assertAllFixedInFile($file);
	}

}
