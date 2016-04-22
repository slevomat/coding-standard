<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

class AssignmentInConditionSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testCorrectFile()
	{
		$resultFile = $this->checkFile(__DIR__ . '/data/noAssignmentsInConditions.php');
		$this->assertNoSniffErrorInFile($resultFile);
	}

	public function dataIncorrectFile()
	{
		$lineNumbers = [];
		foreach (range(3, 6) as $lineNumber) {
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
		$resultFile = $this->checkFile(__DIR__ . '/data/allAssignmentsInConditions.php');
		$this->assertSniffError($resultFile, $lineNumber, AssignmentInConditionSniff::CODE_ASSIGNMENT_IN_CONDITION);
	}

}
