<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

class AssignmentInConditionSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testCorrectFile(): void
	{
		$resultFile = $this->checkFile(__DIR__ . '/data/noAssignmentsInConditions.php');
		$this->assertNoSniffErrorInFile($resultFile);
	}

	public function testIncorrectFile(): void
	{
		$resultFile = $this->checkFile(__DIR__ . '/data/allAssignmentsInConditions.php');
		foreach (range(3, 6) as $lineNumber) {
			$this->assertSniffError($resultFile, $lineNumber, AssignmentInConditionSniff::CODE_ASSIGNMENT_IN_CONDITION);
		}
	}

}
