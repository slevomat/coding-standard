<?php

namespace SlevomatCodingStandard\Sniffs\Types;

class EmptyLinesAroundTypeBracesSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testCorrectCorrectEmptyLines()
	{
		$this->assertNoSniffErrorInFile(
			$this->checkFile(__DIR__ . '/data/correctEmptyLines.php')
		);
	}

	public function testErrorBeforeClosingBrace()
	{
		$report = $this->checkFile(__DIR__ . '/data/errorBeforeClosingBrace.php');
		$this->assertSniffError(
			$report,
			7,
			EmptyLinesAroundTypeBracesSniff::CODE_EMPTY_LINE_BEFORE_CLOSING_BRACE,
			'There must be one empty line before class closing brace.'
		);
	}

	public function testErrorAfterClosingBrace()
	{
		$report = $this->checkFile(__DIR__ . '/data/errorAfterOpeningBrace.php');
		$this->assertSniffError(
			$report,
			4,
			EmptyLinesAroundTypeBracesSniff::CODE_EMPTY_LINE_AFTER_OPENING_BRACE,
			'There must be one empty line after class opening brace.'
		);
	}

}
