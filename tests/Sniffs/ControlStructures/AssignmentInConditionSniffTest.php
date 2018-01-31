<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

class AssignmentInConditionSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testCorrectFile(): void
	{
		$resultFile = self::checkFile(__DIR__ . '/data/noAssignmentsInConditions.php');
		self::assertNoSniffErrorInFile($resultFile);
	}

	public function testIncorrectFile(): void
	{
		$resultFile = self::checkFile(__DIR__ . '/data/allAssignmentsInConditions.php');
		foreach (range(3, 6) as $lineNumber) {
			self::assertSniffError($resultFile, $lineNumber, AssignmentInConditionSniff::CODE_ASSIGNMENT_IN_CONDITION);
		}
	}

}
