<?php

namespace SlevomatCodingStandard\Sniffs\Arrays;

class TrailingArrayCommaSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testCheckFile()
	{
		$resultFile = $this->checkFile(__DIR__ . '/data/trailingCommas.php');
		$this->assertNoSniffError($resultFile, 3);
		$this->assertNoSniffError($resultFile, 10);
		$this->assertNoSniffError($resultFile, 12);
		$this->assertSniffError($resultFile, 18, TrailingArrayCommaSniff::CODE_MISSING_TRAILING_COMMA);
		$this->assertSniffError($resultFile, 26, TrailingArrayCommaSniff::CODE_MISSING_TRAILING_COMMA);
		$this->assertNoSniffError($resultFile, 28);
	}

}
