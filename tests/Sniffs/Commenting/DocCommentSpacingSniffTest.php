<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Commenting;

use SlevomatCodingStandard\Sniffs\TestCase;

class DocCommentSpacingSniffTest extends TestCase
{

	public function testEmptyDocComment(): void
	{
		$report = self::checkFile(__DIR__ . '/data/docCommentSpacingEmptyDocComment.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testInlineDocComment(): void
	{
		$report = self::checkFile(__DIR__ . '/data/docCommentSpacingInlineDocComment.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testDocCommentAtTheBeginningOfFile(): void
	{
		$report = self::checkFile(__DIR__ . '/data/docCommentSpacingDocCommentAtTheBeginningOfFile.php');

		self::assertSame(1, $report->getErrorCount());

		self::assertSniffError($report, 3, DocCommentSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTENT);

		self::assertAllFixedInFile($report);
	}

	public function testOneLineDocComment(): void
	{
		$report = self::checkFile(__DIR__ . '/data/docCommentSpacingOneLineDocComment.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testDefaultSettingsNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/docCommentSpacingDefaultSettingsNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testDefaultSettingsErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/docCommentSpacingDefaultSettingsErrors.php');

		self::assertSame(9, $report->getErrorCount());

		self::assertSniffError($report, 5, DocCommentSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_CONTENT);
		self::assertSniffError($report, 26, DocCommentSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_CONTENT);

		self::assertSniffError($report, 19, DocCommentSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_DESCRIPTION_AND_ANNOTATIONS);
		self::assertSniffError($report, 30, DocCommentSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_DESCRIPTION_AND_ANNOTATIONS);

		self::assertSniffError($report, 42, DocCommentSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_DIFFERENT_ANNOTATIONS_TYPES);
		self::assertSniffError($report, 56, DocCommentSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_DIFFERENT_ANNOTATIONS_TYPES);
		self::assertSniffError($report, 58, DocCommentSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_DIFFERENT_ANNOTATIONS_TYPES);

		self::assertSniffError($report, 9, DocCommentSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTENT);
		self::assertSniffError($report, 42, DocCommentSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTENT);

		self::assertAllFixedInFile($report);
	}

	public function testModifiedSettingsNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/docCommentSpacingModifiedSettingsNoErrors.php', [
			'linesCountBeforeFirstContent' => 1,
			'linesCountBetweenDescriptionAndAnnotations' => 0,
			'linesCountBetweenDifferentAnnotationsTypes' => 1,
			'linesCountAfterLastContent' => 1,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testModifiedSettingsErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/docCommentSpacingModifiedSettingsErrors.php', [
			'linesCountBeforeFirstContent' => 1,
			'linesCountBetweenDescriptionAndAnnotations' => 2,
			'linesCountBetweenDifferentAnnotationsTypes' => 1,
			'linesCountAfterLastContent' => 1,
		]);

		self::assertSame(11, $report->getErrorCount());

		self::assertSniffError($report, 4, DocCommentSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_CONTENT);
		self::assertSniffError($report, 10, DocCommentSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_CONTENT);
		self::assertSniffError($report, 18, DocCommentSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_CONTENT);
		self::assertSniffError($report, 28, DocCommentSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_CONTENT);

		self::assertSniffError($report, 22, DocCommentSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_DESCRIPTION_AND_ANNOTATIONS);
		self::assertSniffError($report, 30, DocCommentSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_DESCRIPTION_AND_ANNOTATIONS);

		self::assertSniffError($report, 31, DocCommentSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_DIFFERENT_ANNOTATIONS_TYPES);
		self::assertSniffError($report, 43, DocCommentSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_DIFFERENT_ANNOTATIONS_TYPES);

		self::assertSniffError($report, 4, DocCommentSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTENT);
		self::assertSniffError($report, 10, DocCommentSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTENT);
		self::assertSniffError($report, 43, DocCommentSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTENT);

		self::assertAllFixedInFile($report);
	}

	public function testAnnotationsGroupsNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/docCommentSpacingAnnotationsGroupsNoErrors.php', [
			'linesCountBetweenAnnotationsGroups' => 1,
			'annotationsGroups' => [
				'@param',
				'@X\\Boo,@X\\,@XX',
				'@throws',
				'@link,@todo',
				'@whatever',
				'@anything',
			],
		], [
			DocCommentSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_ANNOTATIONS_GROUPS,
			DocCommentSpacingSniff::CODE_INCORRECT_ANNOTATIONS_GROUP,
			DocCommentSpacingSniff::CODE_INCORRECT_ORDER_OF_ANNOTATIONS_GROUPS,
			DocCommentSpacingSniff::CODE_INCORRECT_ORDER_OF_ANNOTATIONS_IN_GROUP,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testAnnotationsGroupsErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/docCommentSpacingAnnotationsGroupsErrors.php', [
			'linesCountBetweenAnnotationsGroups' => 1,
			'annotationsGroups' => [
				'@dataProvider',
				'@param',
				'@X\\Boo,@X\\,@XX',
				'@throws',
				'@link,@todo',
				'@whatever',
				'@anything',
				'@phpstan-param,@phpstan-return,@phpstan-',
				'@phpcs:',
			],
		], [
			DocCommentSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_ANNOTATIONS_GROUPS,
			DocCommentSpacingSniff::CODE_INCORRECT_ANNOTATIONS_GROUP,
			DocCommentSpacingSniff::CODE_INCORRECT_ORDER_OF_ANNOTATIONS_GROUPS,
			DocCommentSpacingSniff::CODE_INCORRECT_ORDER_OF_ANNOTATIONS_IN_GROUP,
		]);

		self::assertSame(9, $report->getErrorCount());

		self::assertSniffError($report, 20, DocCommentSpacingSniff::CODE_INCORRECT_ANNOTATIONS_GROUP);
		self::assertSniffError($report, 34, DocCommentSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_ANNOTATIONS_GROUPS);
		self::assertSniffError($report, 44, DocCommentSpacingSniff::CODE_INCORRECT_ORDER_OF_ANNOTATIONS_GROUPS);
		self::assertSniffError($report, 49, DocCommentSpacingSniff::CODE_INCORRECT_ORDER_OF_ANNOTATIONS_IN_GROUP);
		self::assertSniffError($report, 62, DocCommentSpacingSniff::CODE_INCORRECT_ORDER_OF_ANNOTATIONS_GROUPS);
		self::assertSniffError($report, 74, DocCommentSpacingSniff::CODE_INCORRECT_ORDER_OF_ANNOTATIONS_GROUPS);
		self::assertSniffError($report, 83, DocCommentSpacingSniff::CODE_INCORRECT_ANNOTATIONS_GROUP);
		self::assertSniffError($report, 92, DocCommentSpacingSniff::CODE_INCORRECT_ANNOTATIONS_GROUP);
		self::assertSniffError($report, 102, DocCommentSpacingSniff::CODE_INCORRECT_ANNOTATIONS_GROUP);

		self::assertAllFixedInFile($report);
	}

}
