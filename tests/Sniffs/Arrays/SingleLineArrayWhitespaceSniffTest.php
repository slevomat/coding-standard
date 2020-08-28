<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Arrays;

use SlevomatCodingStandard\Sniffs\TestCase;

class SingleLineArrayWhitespaceSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/singleLineArrayWhitespaceNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/singleLineArrayWhitespaceErrors.php', [
			'enableEmptyArrayCheck' => true,
		]);

		self::assertSame(16, $report->getErrorCount());

		self::assertSniffError($report, 3, SingleLineArrayWhitespaceSniff::CODE_SPACE_BEFORE_COMMA, '1 found');
		self::assertSniffError($report, 3, SingleLineArrayWhitespaceSniff::CODE_SPACE_AFTER_COMMA, '2 found');
		self::assertSniffError($report, 3, SingleLineArrayWhitespaceSniff::CODE_SPACE_AFTER_COMMA, '0 found');
		self::assertSniffError($report, 3, SingleLineArrayWhitespaceSniff::CODE_SPACE_BEFORE_COMMA, '5 found');
		self::assertSniffError($report, 3, SingleLineArrayWhitespaceSniff::CODE_SPACE_AFTER_COMMA, '5 found');

		self::assertSniffError($report, 5, SingleLineArrayWhitespaceSniff::CODE_SPACE_AFTER_ARRAY_OPEN, '1 found');
		self::assertSniffError($report, 5, SingleLineArrayWhitespaceSniff::CODE_SPACE_BEFORE_ARRAY_CLOSE, '1 found');
		self::assertSniffError($report, 6, SingleLineArrayWhitespaceSniff::CODE_SPACE_AFTER_ARRAY_OPEN, '1 found');
		self::assertSniffError($report, 6, SingleLineArrayWhitespaceSniff::CODE_SPACE_BEFORE_ARRAY_CLOSE, '1 found');
		self::assertSniffError($report, 7, SingleLineArrayWhitespaceSniff::CODE_SPACE_AFTER_ARRAY_OPEN, '1 found');
		self::assertSniffError($report, 7, SingleLineArrayWhitespaceSniff::CODE_SPACE_BEFORE_ARRAY_CLOSE, '1 found');
		self::assertSniffError($report, 8, SingleLineArrayWhitespaceSniff::CODE_SPACE_AFTER_ARRAY_OPEN, '1 found');
		self::assertSniffError($report, 8, SingleLineArrayWhitespaceSniff::CODE_SPACE_BEFORE_ARRAY_CLOSE, '1 found');
		self::assertSniffError($report, 9, SingleLineArrayWhitespaceSniff::CODE_SPACE_IN_EMPTY_ARRAY);
		self::assertCount(4, $report->getErrors()[8]);

		self::assertAllFixedInFile($report);
	}

	public function testRequireSpacesErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/singleLineArrayWhitespaceRequireSpacesErrors.php', [
			'spacesAroundBrackets' => 1,
			'enableEmptyArrayCheck' => true,
		]);

		self::assertSame(5, $report->getErrorCount());

		self::assertSniffError($report, 3, SingleLineArrayWhitespaceSniff::CODE_SPACE_AFTER_ARRAY_OPEN, '0 found');
		self::assertSniffError($report, 3, SingleLineArrayWhitespaceSniff::CODE_SPACE_BEFORE_ARRAY_CLOSE, '0 found');
		self::assertSniffError($report, 3, SingleLineArrayWhitespaceSniff::CODE_SPACE_IN_EMPTY_ARRAY);

		self::assertAllFixedInFile($report);
	}

}
