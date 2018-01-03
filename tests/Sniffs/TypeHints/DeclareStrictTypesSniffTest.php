<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

class DeclareStrictTypesSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testMultipleOpenTagsInFile(): void
	{
		$this->assertNoSniffErrorInFile($this->checkFile(__DIR__ . '/data/declareStrictTypesMultipleOpenTags.php'));
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
		$report = $this->checkFile($file);
		$this->assertSniffError(
			$report,
			$line,
			DeclareStrictTypesSniff::CODE_DECLARE_STRICT_TYPES_MISSING
		);
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
		$report = $this->checkFile($file);
		$this->assertSniffError(
			$report,
			1,
			DeclareStrictTypesSniff::CODE_INCORRECT_STRICT_TYPES_FORMAT
		);
	}

	public function testEmptyFile(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/declareStrictTypesEmptyFile.php', []);
		$this->assertNoSniffErrorInFile($report);
	}

	public function testDeclareStrictTypesIncorrectFormatNoSpaces(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/declareStrictTypesIncorrectFormatNoSpaces.php', [
			'spacesCountAroundEqualsSign' => 0,
		]);
		$this->assertSniffError(
			$report,
			1,
			DeclareStrictTypesSniff::CODE_INCORRECT_STRICT_TYPES_FORMAT
		);
	}

	public function testDeclareStrictTwoNewlinesBefore(): void
	{
		$file = __DIR__ . '/data/declareStrictTypesTwoNewlinesBefore.php';
		$this->assertNoSniffErrorInFile($this->checkFile($file, [
			'newlinesCountBetweenOpenTagAndDeclare' => ' 2  ',
		]));
	}

	public function testDeclareStrictTwoNewlinesBeforeError(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/declareStrictTypesTwoNewlinesBeforeError.php');
		$this->assertSniffError(
			$report,
			3,
			DeclareStrictTypesSniff::CODE_INCORRECT_WHITESPACE_BETWEEN_OPEN_TAG_AND_DECLARE,
			'There must be a single space between the PHP open tag and declare statement.'
		);
	}

	public function testDeclareStrictTwoNewlinesAfter(): void
	{
		$file = __DIR__ . '/data/declareStrictTypesTwoNewlinesAfter.php';
		$this->assertNoSniffErrorInFile($this->checkFile($file, [
			'newlinesCountAfterDeclare' => ' 2  ',
		], [DeclareStrictTypesSniff::CODE_INCORRECT_WHITESPACE_AFTER_DECLARE]));
	}

	public function testDeclareStrictTwoNewlinesAfterError(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/declareStrictTypesTwoNewlinesAfterError.php');
		$this->assertSniffError(
			$report,
			3,
			DeclareStrictTypesSniff::CODE_INCORRECT_WHITESPACE_AFTER_DECLARE,
			'Expected 2 newlines after declare statement, found 1.'
		);
	}

	public function testDeclareStrictOneSpaceError(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/declareStrictTypesOneSpaceError.php', [
			'newlinesCountBetweenOpenTagAndDeclare' => '2',
		]);
		$this->assertSniffError(
			$report,
			1,
			DeclareStrictTypesSniff::CODE_INCORRECT_WHITESPACE_BETWEEN_OPEN_TAG_AND_DECLARE,
			'Expected 2 newlines between PHP open tag and declare statement, found 0.'
		);
	}

	public function testDeclareStrictOneSpace(): void
	{
		$this->assertNoSniffErrorInFile($this->checkFile(__DIR__ . '/data/declareStrictTypesOneSpace.php'));
	}

	public function testDeclareStrictWithTicks(): void
	{
		$this->assertNoSniffErrorInFile($this->checkFile(__DIR__ . '/data/declareStrictTypesWithTicks.php'));
	}

	public function testFixableNoNewLinesBefore(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/fixableDeclareStrictTypesNoNewLinesBefore.php', [
			'newlinesCountBetweenOpenTagAndDeclare' => 0,
		], [DeclareStrictTypesSniff::CODE_DECLARE_STRICT_TYPES_MISSING, DeclareStrictTypesSniff::CODE_INCORRECT_WHITESPACE_BETWEEN_OPEN_TAG_AND_DECLARE]);
		$this->assertAllFixedInFile($report);
	}

	public function testFixableMissingNoNewLines(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/fixableDeclareStrictTypesMissingNoNewLines.php', [
			'newlinesCountBetweenOpenTagAndDeclare' => 0,
		], [DeclareStrictTypesSniff::CODE_DECLARE_STRICT_TYPES_MISSING, DeclareStrictTypesSniff::CODE_INCORRECT_WHITESPACE_BETWEEN_OPEN_TAG_AND_DECLARE]);
		$this->assertAllFixedInFile($report);
	}

	public function testFixableOneNewLineBefore(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/fixableDeclareStrictTypesOneNewLineBefore.php', [
			'newlinesCountBetweenOpenTagAndDeclare' => 1,
		], [DeclareStrictTypesSniff::CODE_DECLARE_STRICT_TYPES_MISSING, DeclareStrictTypesSniff::CODE_INCORRECT_WHITESPACE_BETWEEN_OPEN_TAG_AND_DECLARE]);
		$this->assertAllFixedInFile($report);
	}

	public function testFixableMissingOneNewLine(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/fixableDeclareStrictTypesMissingOneNewLine.php', [
			'newlinesCountBetweenOpenTagAndDeclare' => 1,
		], [DeclareStrictTypesSniff::CODE_DECLARE_STRICT_TYPES_MISSING, DeclareStrictTypesSniff::CODE_INCORRECT_WHITESPACE_BETWEEN_OPEN_TAG_AND_DECLARE]);
		$this->assertAllFixedInFile($report);
	}

	public function testFixableMoreNewLinesBefore(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/fixableDeclareStrictTypesMoreNewLinesBefore.php', [
			'newlinesCountBetweenOpenTagAndDeclare' => 4,
		], [DeclareStrictTypesSniff::CODE_DECLARE_STRICT_TYPES_MISSING, DeclareStrictTypesSniff::CODE_INCORRECT_WHITESPACE_BETWEEN_OPEN_TAG_AND_DECLARE]);
		$this->assertAllFixedInFile($report);
	}

	public function testFixableMissingMoreNewLines(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/fixableDeclareStrictTypesMissingMoreNewLines.php', [
			'newlinesCountBetweenOpenTagAndDeclare' => 4,
		], [DeclareStrictTypesSniff::CODE_DECLARE_STRICT_TYPES_MISSING, DeclareStrictTypesSniff::CODE_INCORRECT_WHITESPACE_BETWEEN_OPEN_TAG_AND_DECLARE]);
		$this->assertAllFixedInFile($report);
	}

	public function testFixableMissingIncorrectFormatOneSpace(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/fixableDeclareStrictTypesIncorrentFormatOneSpace.php', [], [DeclareStrictTypesSniff::CODE_INCORRECT_STRICT_TYPES_FORMAT]);
		$this->assertAllFixedInFile($report);
	}

	public function testFixableMissingIncorrectFormatNoSpaces(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/fixableDeclareStrictTypesIncorrentFormatNoSpaces.php', [
			'spacesCountAroundEqualsSign' => 0,
		], [DeclareStrictTypesSniff::CODE_INCORRECT_STRICT_TYPES_FORMAT]);
		$this->assertAllFixedInFile($report);
	}

	public function testFixableMissingIncorrectFormatMoreSpaces(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/fixableDeclareStrictTypesIncorrentFormatMoreSpaces.php', [
			'spacesCountAroundEqualsSign' => 4,
		], [DeclareStrictTypesSniff::CODE_INCORRECT_STRICT_TYPES_FORMAT]);
		$this->assertAllFixedInFile($report);
	}

	public function testFixableMissingWithTicks(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/fixableDeclareStrictTypesMissingWithTicks.php', [], [DeclareStrictTypesSniff::CODE_DECLARE_STRICT_TYPES_MISSING]);
		$this->assertAllFixedInFile($report);
	}

	public function testFixableDisabled(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/fixableDeclareStrictTypesDisabled.php', [], [DeclareStrictTypesSniff::CODE_DECLARE_STRICT_TYPES_MISSING]);
		$this->assertAllFixedInFile($report);
	}

	public function testFixableOneNewLineAfter(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/fixableDeclareStrictTypesOneNewLineAfter.php', [
			'newlinesCountAfterDeclare' => 2,
		], [DeclareStrictTypesSniff::CODE_INCORRECT_WHITESPACE_AFTER_DECLARE]);
		$this->assertAllFixedInFile($report);
	}

	public function testFixableNoNewLinesAfter(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/fixableDeclareStrictTypesNoNewLinesAfter.php', [
			'newlinesCountAfterDeclare' => 0,
		], [DeclareStrictTypesSniff::CODE_INCORRECT_WHITESPACE_AFTER_DECLARE]);
		$this->assertAllFixedInFile($report);
	}

	public function testFixableMoreNewLinesAfter(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/fixableDeclareStrictTypesMoreNewLinesAfter.php', [
			'newlinesCountAfterDeclare' => 4,
		], [DeclareStrictTypesSniff::CODE_INCORRECT_WHITESPACE_AFTER_DECLARE]);
		$this->assertAllFixedInFile($report);
	}

}
