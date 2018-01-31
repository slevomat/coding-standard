<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Types;

class EmptyLinesAroundTypeBracesSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testCorrectCorrectEmptyLines(): void
	{
		self::assertNoSniffErrorInFile(self::checkFile(__DIR__ . '/data/correctEmptyLines.php'));
	}

	public function testNoEmptyLineAfterOpeningBrace(): void
	{
		$report = self::checkFile(__DIR__ . '/data/noEmptyLineAfterOpeningBrace.php', [], [EmptyLinesAroundTypeBracesSniff::CODE_NO_EMPTY_LINE_AFTER_OPENING_BRACE]);

		self::assertSame(1, $report->getErrorCount());

		self::assertSniffError($report, 4, EmptyLinesAroundTypeBracesSniff::CODE_NO_EMPTY_LINE_AFTER_OPENING_BRACE);

		self::assertAllFixedInFile($report);
	}

	public function testMultipleEmptyLinesAfterOpeningBrace(): void
	{
		$report = self::checkFile(__DIR__ . '/data/multipleEmptyLinesAfterOpeningBrace.php', [], [EmptyLinesAroundTypeBracesSniff::CODE_MULTIPLE_EMPTY_LINES_AFTER_OPENING_BRACE]);

		self::assertSame(1, $report->getErrorCount());

		self::assertSniffError($report, 4, EmptyLinesAroundTypeBracesSniff::CODE_MULTIPLE_EMPTY_LINES_AFTER_OPENING_BRACE);

		self::assertAllFixedInFile($report);
	}

	public function testNoEmptyLineBeforeClosingBrace(): void
	{
		$report = self::checkFile(__DIR__ . '/data/noEmptyLineBeforeClosingBrace.php', [], [EmptyLinesAroundTypeBracesSniff::CODE_NO_EMPTY_LINE_BEFORE_CLOSING_BRACE]);

		self::assertSame(1, $report->getErrorCount());

		self::assertSniffError($report, 10, EmptyLinesAroundTypeBracesSniff::CODE_NO_EMPTY_LINE_BEFORE_CLOSING_BRACE);

		self::assertAllFixedInFile($report);
	}

	public function testMultipleEmptyLinesBeforeClosingBrace(): void
	{
		$report = self::checkFile(__DIR__ . '/data/multipleEmptyLinesBeforeClosingBrace.php', [], [EmptyLinesAroundTypeBracesSniff::CODE_MULTIPLE_EMPTY_LINES_BEFORE_CLOSING_BRACE]);

		self::assertSame(1, $report->getErrorCount());

		self::assertSniffError($report, 13, EmptyLinesAroundTypeBracesSniff::CODE_MULTIPLE_EMPTY_LINES_BEFORE_CLOSING_BRACE);

		self::assertAllFixedInFile($report);
	}

	public function testCorrectCorrectEmptyLinesWithZeroLines(): void
	{
		$report = self::checkFile(__DIR__ . '/data/correctEmptyLinesZeroLines.php', [
			'linesCountAfterOpeningBrace' => 0,
			'linesCountBeforeClosingBrace' => 0,
		]);

		self::assertNoSniffErrorInFile($report);
	}

	public function testOneLineAfterOpeningBraceWithZeroExpected(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/oneEmptyLineAfterOpeningBraceWithZeroExpected.php',
			['linesCountAfterOpeningBrace' => 0],
			[EmptyLinesAroundTypeBracesSniff::CODE_INCORRECT_EMPTY_LINES_AFTER_OPENING_BRACE]
		);

		self::assertSame(1, $report->getErrorCount());

		self::assertSniffError($report, 4, EmptyLinesAroundTypeBracesSniff::CODE_INCORRECT_EMPTY_LINES_AFTER_OPENING_BRACE);

		self::assertAllFixedInFile($report);
	}

	public function testOneLineBeforeClosingBraceWithZeroExpected(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/oneEmptyLineBeforeClosingBraceWithZeroExpected.php',
			['linesCountBeforeClosingBrace' => 0],
			[EmptyLinesAroundTypeBracesSniff::CODE_INCORRECT_EMPTY_LINES_BEFORE_CLOSING_BRACE]
		);

		self::assertSame(1, $report->getErrorCount());

		self::assertSniffError($report, 10, EmptyLinesAroundTypeBracesSniff::CODE_INCORRECT_EMPTY_LINES_BEFORE_CLOSING_BRACE);

		self::assertAllFixedInFile($report);
	}

	public function testCorrectCorrectEmptyLinesWithTwoLines(): void
	{
		$report = self::checkFile(__DIR__ . '/data/correctEmptyLinesTwoLines.php', [
			'linesCountAfterOpeningBrace' => 2,
			'linesCountBeforeClosingBrace' => 2,
		]);

		self::assertNoSniffErrorInFile($report);
	}

	public function testOneLineAfterOpeningBraceWithTwoExpected(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/oneEmptyLineAfterOpeningBraceWithTwoExpected.php',
			['linesCountAfterOpeningBrace' => 2],
			[EmptyLinesAroundTypeBracesSniff::CODE_INCORRECT_EMPTY_LINES_AFTER_OPENING_BRACE]
		);

		self::assertSame(1, $report->getErrorCount());

		self::assertSniffError($report, 4, EmptyLinesAroundTypeBracesSniff::CODE_INCORRECT_EMPTY_LINES_AFTER_OPENING_BRACE);

		self::assertAllFixedInFile($report);
	}

	public function testOneLineBeforeClosingBraceWithTwoExpected(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/oneEmptyLineBeforeClosingBraceWithTwoExpected.php',
			['linesCountBeforeClosingBrace' => 2],
			[EmptyLinesAroundTypeBracesSniff::CODE_INCORRECT_EMPTY_LINES_BEFORE_CLOSING_BRACE]
		);

		self::assertSame(1, $report->getErrorCount());

		self::assertSniffError($report, 12, EmptyLinesAroundTypeBracesSniff::CODE_INCORRECT_EMPTY_LINES_BEFORE_CLOSING_BRACE);

		self::assertAllFixedInFile($report);
	}

}
