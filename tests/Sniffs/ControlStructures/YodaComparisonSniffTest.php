<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

class YodaComparisonSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testCorrectFile()
	{
		$resultFile = $this->checkFile(__DIR__ . '/data/noYodaComparisons.php');
		$this->assertNoSniffErrorInFile($resultFile);
	}

	public function dataIncorrectFile(): array
	{
		$lineNumbers = [];
		foreach (range(3, 22) as $lineNumber) {
			$lineNumbers[$lineNumber] = [$lineNumber];
		}

		return $lineNumbers;
	}

	/**
	 * @dataProvider dataIncorrectFile
	 * @param int $lineNumber
	 */
	public function testIncorrectFile(int $lineNumber)
	{
		$resultFile = $this->checkFile(__DIR__ . '/data/allYodaComparisons.php');
		$this->assertSniffError($resultFile, $lineNumber, YodaComparisonSniff::CODE_YODA_COMPARISON);
	}

	public function testFixable()
	{
		$report = $this->checkFile(__DIR__ . '/data/fixableYodaComparisons.php', [], [YodaComparisonSniff::CODE_YODA_COMPARISON]);
		$this->assertAllFixedInFile($report);
	}

}
