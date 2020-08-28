<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use SlevomatCodingStandard\Sniffs\TestCase;

class EmptyLinesAroundClassBracesSniffTest extends TestCase
{

	public function testCorrectCorrectEmptyLines(): void
	{
		$report = self::checkFile(__DIR__ . '/data/classBracesCorrectEmptyLines.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testNoEmptyLineAfterOpeningBrace(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/classBracesNoEmptyLineAfterOpeningBrace.php',
			[],
			[EmptyLinesAroundClassBracesSniff::CODE_NO_EMPTY_LINE_AFTER_OPENING_BRACE]
		);

		self::assertSame(1, $report->getErrorCount());

		self::assertSniffError($report, 4, EmptyLinesAroundClassBracesSniff::CODE_NO_EMPTY_LINE_AFTER_OPENING_BRACE);

		self::assertAllFixedInFile($report);
	}

	public function testMultipleEmptyLinesAfterOpeningBrace(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/classBracesMultipleEmptyLinesAfterOpeningBrace.php',
			[],
			[EmptyLinesAroundClassBracesSniff::CODE_MULTIPLE_EMPTY_LINES_AFTER_OPENING_BRACE]
		);

		self::assertSame(1, $report->getErrorCount());

		self::assertSniffError($report, 4, EmptyLinesAroundClassBracesSniff::CODE_MULTIPLE_EMPTY_LINES_AFTER_OPENING_BRACE);

		self::assertAllFixedInFile($report);
	}

	public function testNoEmptyLineBeforeClosingBrace(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/classBracesNoEmptyLineBeforeClosingBrace.php',
			[],
			[EmptyLinesAroundClassBracesSniff::CODE_NO_EMPTY_LINE_BEFORE_CLOSING_BRACE]
		);

		self::assertSame(1, $report->getErrorCount());

		self::assertSniffError($report, 10, EmptyLinesAroundClassBracesSniff::CODE_NO_EMPTY_LINE_BEFORE_CLOSING_BRACE);

		self::assertAllFixedInFile($report);
	}

	public function testMultipleEmptyLinesBeforeClosingBrace(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/classBracesMultipleEmptyLinesBeforeClosingBrace.php',
			[],
			[EmptyLinesAroundClassBracesSniff::CODE_MULTIPLE_EMPTY_LINES_BEFORE_CLOSING_BRACE]
		);

		self::assertSame(1, $report->getErrorCount());

		self::assertSniffError($report, 13, EmptyLinesAroundClassBracesSniff::CODE_MULTIPLE_EMPTY_LINES_BEFORE_CLOSING_BRACE);

		self::assertAllFixedInFile($report);
	}

	public function testCorrectCorrectEmptyLinesWithZeroLines(): void
	{
		$report = self::checkFile(__DIR__ . '/data/classBracesCorrectEmptyLinesZeroLines.php', [
			'linesCountAfterOpeningBrace' => 0,
			'linesCountBeforeClosingBrace' => 0,
		]);

		self::assertNoSniffErrorInFile($report);
	}

	public function testOneLineAfterOpeningBraceWithZeroExpected(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/classBracesOneEmptyLineAfterOpeningBraceWithZeroExpected.php',
			['linesCountAfterOpeningBrace' => 0],
			[EmptyLinesAroundClassBracesSniff::CODE_INCORRECT_EMPTY_LINES_AFTER_OPENING_BRACE]
		);

		self::assertSame(1, $report->getErrorCount());

		self::assertSniffError($report, 4, EmptyLinesAroundClassBracesSniff::CODE_INCORRECT_EMPTY_LINES_AFTER_OPENING_BRACE);

		self::assertAllFixedInFile($report);
	}

	public function testOneLineBeforeClosingBraceWithZeroExpected(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/classBracesOneEmptyLineBeforeClosingBraceWithZeroExpected.php',
			['linesCountBeforeClosingBrace' => 0],
			[EmptyLinesAroundClassBracesSniff::CODE_INCORRECT_EMPTY_LINES_BEFORE_CLOSING_BRACE]
		);

		self::assertSame(1, $report->getErrorCount());

		self::assertSniffError($report, 10, EmptyLinesAroundClassBracesSniff::CODE_INCORRECT_EMPTY_LINES_BEFORE_CLOSING_BRACE);

		self::assertAllFixedInFile($report);
	}

	public function testCorrectCorrectEmptyLinesWithTwoLines(): void
	{
		$report = self::checkFile(__DIR__ . '/data/classBracesCorrectEmptyLinesTwoLines.php', [
			'linesCountAfterOpeningBrace' => 2,
			'linesCountBeforeClosingBrace' => 2,
		]);

		self::assertNoSniffErrorInFile($report);
	}

	public function testOneLineAfterOpeningBraceWithTwoExpected(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/classBracesOneEmptyLineAfterOpeningBraceWithTwoExpected.php',
			['linesCountAfterOpeningBrace' => 2],
			[EmptyLinesAroundClassBracesSniff::CODE_INCORRECT_EMPTY_LINES_AFTER_OPENING_BRACE]
		);

		self::assertSame(1, $report->getErrorCount());

		self::assertSniffError($report, 4, EmptyLinesAroundClassBracesSniff::CODE_INCORRECT_EMPTY_LINES_AFTER_OPENING_BRACE);

		self::assertAllFixedInFile($report);
	}

	public function testOneLineBeforeClosingBraceWithTwoExpected(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/classBracesOneEmptyLineBeforeClosingBraceWithTwoExpected.php',
			['linesCountBeforeClosingBrace' => 2],
			[EmptyLinesAroundClassBracesSniff::CODE_INCORRECT_EMPTY_LINES_BEFORE_CLOSING_BRACE]
		);

		self::assertSame(1, $report->getErrorCount());

		self::assertSniffError($report, 12, EmptyLinesAroundClassBracesSniff::CODE_INCORRECT_EMPTY_LINES_BEFORE_CLOSING_BRACE);

		self::assertAllFixedInFile($report);
	}

}
