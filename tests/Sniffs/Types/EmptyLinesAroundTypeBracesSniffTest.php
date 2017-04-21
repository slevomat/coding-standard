<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Types;

class EmptyLinesAroundTypeBracesSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testCorrectCorrectEmptyLines()
	{
		$this->assertNoSniffErrorInFile($this->checkFile(__DIR__ . '/data/correctEmptyLines.php'));
	}

	public function testNoEmptyLineAfterOpeningBrace()
	{
		$report = $this->checkFile(__DIR__ . '/data/noEmptyLineAfterOpeningBrace.php', [], [EmptyLinesAroundTypeBracesSniff::CODE_NO_EMPTY_LINE_AFTER_OPENING_BRACE]);

		$this->assertSame(1, $report->getErrorCount());

		$this->assertSniffError($report, 4, EmptyLinesAroundTypeBracesSniff::CODE_NO_EMPTY_LINE_AFTER_OPENING_BRACE);

		$this->assertAllFixedInFile($report);
	}

	public function testMultipleEmptyLinesAfterOpeningBrace()
	{
		$report = $this->checkFile(__DIR__ . '/data/multipleEmptyLinesAfterOpeningBrace.php', [], [EmptyLinesAroundTypeBracesSniff::CODE_MULTIPLE_EMPTY_LINES_AFTER_OPENING_BRACE]);

		$this->assertSame(1, $report->getErrorCount());

		$this->assertSniffError($report, 4, EmptyLinesAroundTypeBracesSniff::CODE_MULTIPLE_EMPTY_LINES_AFTER_OPENING_BRACE);

		$this->assertAllFixedInFile($report);
	}

	public function testNoEmptyLineBeforeClosingBrace()
	{
		$report = $this->checkFile(__DIR__ . '/data/noEmptyLineBeforeClosingBrace.php', [], [EmptyLinesAroundTypeBracesSniff::CODE_NO_EMPTY_LINE_BEFORE_CLOSING_BRACE]);

		$this->assertSame(1, $report->getErrorCount());

		$this->assertSniffError($report, 10, EmptyLinesAroundTypeBracesSniff::CODE_NO_EMPTY_LINE_BEFORE_CLOSING_BRACE);

		$this->assertAllFixedInFile($report);
	}

	public function testMultipleEmptyLinesBeforeClosingBrace()
	{
		$report = $this->checkFile(__DIR__ . '/data/multipleEmptyLinesBeforeClosingBrace.php', [], [EmptyLinesAroundTypeBracesSniff::CODE_MULTIPLE_EMPTY_LINES_BEFORE_CLOSING_BRACE]);

		$this->assertSame(1, $report->getErrorCount());

		$this->assertSniffError($report, 13, EmptyLinesAroundTypeBracesSniff::CODE_MULTIPLE_EMPTY_LINES_BEFORE_CLOSING_BRACE);

		$this->assertAllFixedInFile($report);
	}

	public function testCorrectCorrectEmptyLinesWithZeroLines()
	{
		$report = $this->checkFile(__DIR__ . '/data/correctEmptyLinesZeroLines.php', [
			'linesCountAfterOpeningBrace' => 0,
			'linesCountBeforeClosingBrace' => 0,
		]);

		$this->assertNoSniffErrorInFile($report);
	}

	public function testOneLineAfterOpeningBraceWithZeroExpected()
	{
		$report = $this->checkFile(
			__DIR__ . '/data/oneEmptyLineAfterOpeningBraceWithZeroExpected.php',
			['linesCountAfterOpeningBrace' => 0],
			[EmptyLinesAroundTypeBracesSniff::CODE_INCORRECT_EMPTY_LINES_AFTER_OPENING_BRACE]
		);

		$this->assertSame(1, $report->getErrorCount());

		$this->assertSniffError($report, 4, EmptyLinesAroundTypeBracesSniff::CODE_INCORRECT_EMPTY_LINES_AFTER_OPENING_BRACE);

		$this->assertAllFixedInFile($report);
	}

	public function testOneLineBeforeClosingBraceWithZeroExpected()
	{
		$report = $this->checkFile(
			__DIR__ . '/data/oneEmptyLineBeforeClosingBraceWithZeroExpected.php',
			['linesCountBeforeClosingBrace' => 0],
			[EmptyLinesAroundTypeBracesSniff::CODE_INCORRECT_EMPTY_LINES_BEFORE_CLOSING_BRACE]
		);

		$this->assertSame(1, $report->getErrorCount());

		$this->assertSniffError($report, 10, EmptyLinesAroundTypeBracesSniff::CODE_INCORRECT_EMPTY_LINES_BEFORE_CLOSING_BRACE);

		$this->assertAllFixedInFile($report);
	}

	public function testCorrectCorrectEmptyLinesWithTwoLines()
	{
		$report = $this->checkFile(__DIR__ . '/data/correctEmptyLinesTwoLines.php', [
			'linesCountAfterOpeningBrace' => 2,
			'linesCountBeforeClosingBrace' => 2,
		]);

		$this->assertNoSniffErrorInFile($report);
	}

	public function testOneLineAfterOpeningBraceWithTwoExpected()
	{
		$report = $this->checkFile(
			__DIR__ . '/data/oneEmptyLineAfterOpeningBraceWithTwoExpected.php',
			['linesCountAfterOpeningBrace' => 2],
			[EmptyLinesAroundTypeBracesSniff::CODE_INCORRECT_EMPTY_LINES_AFTER_OPENING_BRACE]
		);

		$this->assertSame(1, $report->getErrorCount());

		$this->assertSniffError($report, 4, EmptyLinesAroundTypeBracesSniff::CODE_INCORRECT_EMPTY_LINES_AFTER_OPENING_BRACE);

		$this->assertAllFixedInFile($report);
	}

	public function testOneLineBeforeClosingBraceWithTwoExpected()
	{
		$report = $this->checkFile(
			__DIR__ . '/data/oneEmptyLineBeforeClosingBraceWithTwoExpected.php',
			['linesCountBeforeClosingBrace' => 2],
			[EmptyLinesAroundTypeBracesSniff::CODE_INCORRECT_EMPTY_LINES_BEFORE_CLOSING_BRACE]
		);

		$this->assertSame(1, $report->getErrorCount());

		$this->assertSniffError($report, 12, EmptyLinesAroundTypeBracesSniff::CODE_INCORRECT_EMPTY_LINES_BEFORE_CLOSING_BRACE);

		$this->assertAllFixedInFile($report);
	}

}
