<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use SlevomatCodingStandard\Sniffs\TestCase;

class DeclareStrictTypesSniffTest extends TestCase
{

	public function testMultipleOpenTagsInFile(): void
	{
		$report = self::checkFile(__DIR__ . '/data/declareStrictTypesMultipleOpenTags.php');
		self::assertNoSniffErrorInFile($report);
	}

	/**
	 * @return mixed[][]
	 */
	public function dataDeclareStrictTypesMissing(): array
	{
		return [
			[
				__DIR__ . '/data/declareStrictTypesMissing.php',
				1,
			],
			[
				__DIR__ . '/data/declareStrictTypesMissingEmptyFile.php',
				1,
			],
			[
				__DIR__ . '/data/declareTicks.php',
				3,
			],
			[
				__DIR__ . '/data/declareStrictTypesZero.php',
				3,
			],
		];
	}

	/**
	 * @dataProvider dataDeclareStrictTypesMissing
	 * @param string $file
	 * @param int $line
	 */
	public function testDeclareStrictTypesMissing(string $file, int $line): void
	{
		$report = self::checkFile($file);
		self::assertSniffError($report, $line, DeclareStrictTypesSniff::CODE_DECLARE_STRICT_TYPES_MISSING);
	}

	/**
	 * @return string[][]
	 */
	public function dataDeclareStrictTypesIncorrectFormat(): array
	{
		return [
			[
				__DIR__ . '/data/declareStrictTypesIncorrectFormat1.php',
			],
			[
				__DIR__ . '/data/declareStrictTypesIncorrectFormat2.php',
			],
			[
				__DIR__ . '/data/declareStrictTypesIncorrectFormat3.php',
			],
		];
	}

	/**
	 * @dataProvider dataDeclareStrictTypesIncorrectFormat
	 * @param string $file
	 */
	public function testDeclareStrictTypesIncorrectFormat(string $file): void
	{
		$report = self::checkFile($file);
		self::assertSniffError($report, 1, DeclareStrictTypesSniff::CODE_INCORRECT_STRICT_TYPES_FORMAT);
	}

	public function testEmptyFile(): void
	{
		$report = self::checkFile(__DIR__ . '/data/declareStrictTypesEmptyFile.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testDeclareStrictTypesIncorrectFormatNoSpaces(): void
	{
		$report = self::checkFile(__DIR__ . '/data/declareStrictTypesIncorrectFormatNoSpaces.php', [
			'spacesCountAroundEqualsSign' => 0,
		]);
		self::assertSniffError($report, 1, DeclareStrictTypesSniff::CODE_INCORRECT_STRICT_TYPES_FORMAT);
	}

	public function testDeclareStrictTwoNewlinesBefore(): void
	{
		$report = self::checkFile(__DIR__ . '/data/declareStrictTypesTwoNewlinesBefore.php', [
			'newlinesCountBetweenOpenTagAndDeclare' => ' 2  ',
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testDeclareStrictTwoNewlinesBeforeError(): void
	{
		$report = self::checkFile(__DIR__ . '/data/declareStrictTypesTwoNewlinesBeforeError.php');
		self::assertSniffError(
			$report,
			3,
			DeclareStrictTypesSniff::CODE_INCORRECT_WHITESPACE_BETWEEN_OPEN_TAG_AND_DECLARE,
			'There must be a single space between the PHP open tag and declare statement.'
		);
	}

	public function testDeclareStrictTwoNewlinesAfter(): void
	{
		$report = self::checkFile(__DIR__ . '/data/declareStrictTypesTwoNewlinesAfter.php', [
			'newlinesCountAfterDeclare' => ' 2  ',
		], [DeclareStrictTypesSniff::CODE_INCORRECT_WHITESPACE_AFTER_DECLARE]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testDeclareStrictTwoNewlinesAfterError(): void
	{
		$report = self::checkFile(__DIR__ . '/data/declareStrictTypesTwoNewlinesAfterError.php');
		self::assertSniffError(
			$report,
			3,
			DeclareStrictTypesSniff::CODE_INCORRECT_WHITESPACE_AFTER_DECLARE,
			'Expected 2 newlines after declare statement, found 1.'
		);
	}

	public function testDeclareStrictOneSpaceError(): void
	{
		$report = self::checkFile(__DIR__ . '/data/declareStrictTypesOneSpaceError.php', [
			'newlinesCountBetweenOpenTagAndDeclare' => '2',
		]);
		self::assertSniffError(
			$report,
			1,
			DeclareStrictTypesSniff::CODE_INCORRECT_WHITESPACE_BETWEEN_OPEN_TAG_AND_DECLARE,
			'Expected 2 newlines between PHP open tag and declare statement, found 0.'
		);
	}

	public function testDeclareStrictOneSpace(): void
	{
		$report = self::checkFile(__DIR__ . '/data/declareStrictTypesOneSpace.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testDeclareStrictWithFileCommentAbove(): void
	{
		$report = self::checkFile(__DIR__ . '/data/declareStrictTypesWithFileCommentAbove.php', [
			'newlinesCountBetweenOpenTagAndDeclare' => 2,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testDeclareStrictWithTicks(): void
	{
		$report = self::checkFile(__DIR__ . '/data/declareStrictTypesWithTicks.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testFixableNoNewLinesBefore(): void
	{
		$report = self::checkFile(__DIR__ . '/data/fixableDeclareStrictTypesNoNewLinesBefore.php', [
			'newlinesCountBetweenOpenTagAndDeclare' => 0,
		], [DeclareStrictTypesSniff::CODE_DECLARE_STRICT_TYPES_MISSING, DeclareStrictTypesSniff::CODE_INCORRECT_WHITESPACE_BETWEEN_OPEN_TAG_AND_DECLARE]);
		self::assertAllFixedInFile($report);
	}

	public function testFixableMissingNoNewLines(): void
	{
		$report = self::checkFile(__DIR__ . '/data/fixableDeclareStrictTypesMissingNoNewLines.php', [
			'newlinesCountBetweenOpenTagAndDeclare' => 0,
		], [DeclareStrictTypesSniff::CODE_DECLARE_STRICT_TYPES_MISSING, DeclareStrictTypesSniff::CODE_INCORRECT_WHITESPACE_BETWEEN_OPEN_TAG_AND_DECLARE]);
		self::assertAllFixedInFile($report);
	}

	public function testFixableOneNewLineBefore(): void
	{
		$report = self::checkFile(__DIR__ . '/data/fixableDeclareStrictTypesOneNewLineBefore.php', [
			'newlinesCountBetweenOpenTagAndDeclare' => 1,
		], [DeclareStrictTypesSniff::CODE_DECLARE_STRICT_TYPES_MISSING, DeclareStrictTypesSniff::CODE_INCORRECT_WHITESPACE_BETWEEN_OPEN_TAG_AND_DECLARE]);
		self::assertAllFixedInFile($report);
	}

	public function testFixableMissingOneNewLine(): void
	{
		$report = self::checkFile(__DIR__ . '/data/fixableDeclareStrictTypesMissingOneNewLine.php', [
			'newlinesCountBetweenOpenTagAndDeclare' => 1,
		], [DeclareStrictTypesSniff::CODE_DECLARE_STRICT_TYPES_MISSING, DeclareStrictTypesSniff::CODE_INCORRECT_WHITESPACE_BETWEEN_OPEN_TAG_AND_DECLARE]);
		self::assertAllFixedInFile($report);
	}

	public function testFixableMoreNewLinesBefore(): void
	{
		$report = self::checkFile(__DIR__ . '/data/fixableDeclareStrictTypesMoreNewLinesBefore.php', [
			'newlinesCountBetweenOpenTagAndDeclare' => 4,
		], [DeclareStrictTypesSniff::CODE_DECLARE_STRICT_TYPES_MISSING, DeclareStrictTypesSniff::CODE_INCORRECT_WHITESPACE_BETWEEN_OPEN_TAG_AND_DECLARE]);
		self::assertAllFixedInFile($report);
	}

	public function testFixableMissingMoreNewLines(): void
	{
		$report = self::checkFile(__DIR__ . '/data/fixableDeclareStrictTypesMissingMoreNewLines.php', [
			'newlinesCountBetweenOpenTagAndDeclare' => 4,
		], [DeclareStrictTypesSniff::CODE_DECLARE_STRICT_TYPES_MISSING, DeclareStrictTypesSniff::CODE_INCORRECT_WHITESPACE_BETWEEN_OPEN_TAG_AND_DECLARE]);
		self::assertAllFixedInFile($report);
	}

	public function testFixableMissingIncorrectFormatOneSpace(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/fixableDeclareStrictTypesIncorrentFormatOneSpace.php',
			[],
			[DeclareStrictTypesSniff::CODE_INCORRECT_STRICT_TYPES_FORMAT]
		);
		self::assertAllFixedInFile($report);
	}

	public function testFixableMissingIncorrectFormatNoSpaces(): void
	{
		$report = self::checkFile(__DIR__ . '/data/fixableDeclareStrictTypesIncorrentFormatNoSpaces.php', [
			'spacesCountAroundEqualsSign' => 0,
		], [DeclareStrictTypesSniff::CODE_INCORRECT_STRICT_TYPES_FORMAT]);
		self::assertAllFixedInFile($report);
	}

	public function testFixableMissingIncorrectFormatMoreSpaces(): void
	{
		$report = self::checkFile(__DIR__ . '/data/fixableDeclareStrictTypesIncorrentFormatMoreSpaces.php', [
			'spacesCountAroundEqualsSign' => 4,
		], [DeclareStrictTypesSniff::CODE_INCORRECT_STRICT_TYPES_FORMAT]);
		self::assertAllFixedInFile($report);
	}

	public function testFixableMissingWithTicks(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/fixableDeclareStrictTypesMissingWithTicks.php',
			[],
			[DeclareStrictTypesSniff::CODE_DECLARE_STRICT_TYPES_MISSING]
		);
		self::assertAllFixedInFile($report);
	}

	public function testFixableDisabled(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/fixableDeclareStrictTypesDisabled.php',
			[],
			[DeclareStrictTypesSniff::CODE_DECLARE_STRICT_TYPES_MISSING]
		);
		self::assertAllFixedInFile($report);
	}

	public function testFixableOneNewLineAfter(): void
	{
		$report = self::checkFile(__DIR__ . '/data/fixableDeclareStrictTypesOneNewLineAfter.php', [
			'newlinesCountAfterDeclare' => 2,
		], [DeclareStrictTypesSniff::CODE_INCORRECT_WHITESPACE_AFTER_DECLARE]);
		self::assertAllFixedInFile($report);
	}

	public function testFixableNoNewLinesAfter(): void
	{
		$report = self::checkFile(__DIR__ . '/data/fixableDeclareStrictTypesNoNewLinesAfter.php', [
			'newlinesCountAfterDeclare' => 0,
		], [DeclareStrictTypesSniff::CODE_INCORRECT_WHITESPACE_AFTER_DECLARE]);
		self::assertAllFixedInFile($report);
	}

	public function testFixableMoreNewLinesAfter(): void
	{
		$report = self::checkFile(__DIR__ . '/data/fixableDeclareStrictTypesMoreNewLinesAfter.php', [
			'newlinesCountAfterDeclare' => 4,
		], [DeclareStrictTypesSniff::CODE_INCORRECT_WHITESPACE_AFTER_DECLARE]);
		self::assertAllFixedInFile($report);
	}

}
