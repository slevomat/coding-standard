<?php

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

class YodaComparisonSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testCorrectFile()
	{
		$resultFile = $this->checkFile(__DIR__ . '/data/noYodaComparisons.php');
		$this->assertNoSniffErrorInFile($resultFile);
	}

	public function dataIncorrectFile()
	{
		$lineNumbers = [];
		foreach (range(3, 22) as $lineNumber) {
			$lineNumbers[$lineNumber] = [$lineNumber];
		}

		return $lineNumbers;
	}

	/**
	 * @dataProvider dataIncorrectFile
	 * @param integer $lineNumber
	 */
	public function testIncorrectFile($lineNumber)
	{
		$resultFile = $this->checkFile(__DIR__ . '/data/allYodaComparisons.php');
		$this->assertSniffError($resultFile, $lineNumber, YodaComparisonSniff::CODE_YODA_COMPARISON);
	}

}
