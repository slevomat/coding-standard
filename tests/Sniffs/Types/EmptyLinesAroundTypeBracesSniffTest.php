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

}
