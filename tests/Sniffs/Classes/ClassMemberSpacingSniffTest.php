<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use SlevomatCodingStandard\Sniffs\TestCase;

class ClassMemberSpacingSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/classMemberSpacingNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/classMemberSpacingErrors.php');

		self::assertSame(9, $report->getErrorCount());

		self::assertSniffError($report, 15, ClassMemberSpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_BETWEEN_MEMBERS);
		self::assertSniffError($report, 21, ClassMemberSpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_BETWEEN_MEMBERS);
		self::assertSniffError($report, 33, ClassMemberSpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_BETWEEN_MEMBERS);
		self::assertSniffError($report, 38, ClassMemberSpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_BETWEEN_MEMBERS);
		self::assertSniffError($report, 44, ClassMemberSpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_BETWEEN_MEMBERS);
		self::assertSniffError($report, 47, ClassMemberSpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_BETWEEN_MEMBERS);
		self::assertSniffError($report, 50, ClassMemberSpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_BETWEEN_MEMBERS);
		self::assertSniffError($report, 60, ClassMemberSpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_BETWEEN_MEMBERS);
		self::assertSniffError($report, 69, ClassMemberSpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_BETWEEN_MEMBERS);

		self::assertAllFixedInFile($report);
	}

	public function testErrorsWithModifiedLinesCount(): void
	{
		$report = self::checkFile(__DIR__ . '/data/classMemberSpacingErrors.php', [
			'linesCountBetweenMembers' => 2,
		]);

		self::assertSame(2, $report->getErrorCount());

		self::assertSniffError($report, 21, ClassMemberSpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_BETWEEN_MEMBERS);
		self::assertSniffError($report, 69, ClassMemberSpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_BETWEEN_MEMBERS);
	}

}
