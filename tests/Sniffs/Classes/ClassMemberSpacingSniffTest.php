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

		self::assertSame(10, $report->getErrorCount());

		self::assertSniffError($report, 15, ClassMemberSpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_BETWEEN_MEMBERS);
		self::assertSniffError($report, 21, ClassMemberSpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_BETWEEN_MEMBERS);
		self::assertSniffError($report, 33, ClassMemberSpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_BETWEEN_MEMBERS);
		self::assertSniffError($report, 38, ClassMemberSpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_BETWEEN_MEMBERS);
		self::assertSniffError($report, 44, ClassMemberSpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_BETWEEN_MEMBERS);
		self::assertSniffError($report, 47, ClassMemberSpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_BETWEEN_MEMBERS);
		self::assertSniffError($report, 50, ClassMemberSpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_BETWEEN_MEMBERS);
		self::assertSniffError($report, 58, ClassMemberSpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_BETWEEN_MEMBERS);
		self::assertSniffError($report, 70, ClassMemberSpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_BETWEEN_MEMBERS);
		self::assertSniffError($report, 79, ClassMemberSpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_BETWEEN_MEMBERS);

		self::assertAllFixedInFile($report);
	}

	public function testErrorsWithModifiedLinesCount(): void
	{
		$report = self::checkFile(__DIR__ . '/data/classMemberSpacingErrors.php', [
			'linesCountBetweenMembers' => 2,
		]);

		self::assertSame(3, $report->getErrorCount());

		self::assertSniffError($report, 21, ClassMemberSpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_BETWEEN_MEMBERS);
		self::assertSniffError($report, 58, ClassMemberSpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_BETWEEN_MEMBERS);
		self::assertSniffError($report, 79, ClassMemberSpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_BETWEEN_MEMBERS);
	}

	public function testErrorsDuringLiveCoding(): void
	{
		$report = self::checkFile(__DIR__ . '/data/classMemberSpacingLiveCodingErrors.php');

		self::assertSame(1, $report->getErrorCount());
		self::assertSame(0, $report->getFixableCount());

		self::assertSniffError($report, 11, ClassMemberSpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_BETWEEN_MEMBERS);
	}

}
