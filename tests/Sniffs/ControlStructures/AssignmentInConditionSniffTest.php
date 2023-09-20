<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use SlevomatCodingStandard\Sniffs\TestCase;
use function range;

class AssignmentInConditionSniffTest extends TestCase
{

	public function testCorrectFile(): void
	{
		$resultFile = self::checkFile(__DIR__ . '/data/noAssignmentsInConditions.php');
		self::assertNoSniffErrorInFile($resultFile);
	}

	public function testIncorrectFile(): void
	{
		$resultFile = self::checkFile(__DIR__ . '/data/allAssignmentsInConditions.php');
		self::assertEquals(6, $resultFile->getErrorCount());
		foreach (range(3, 8) as $lineNumber) {
			self::assertSniffError($resultFile, $lineNumber, AssignmentInConditionSniff::CODE_ASSIGNMENT_IN_CONDITION);
		}
	}

	public function testNoErrorsWithIgnoreAssignmentsInsideFunctionCalls(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/noAssignmentsInConditionsIgnoreAssignmentsInsideFunctionCalls.php',
			[
				'ignoreAssignmentsInsideFunctionCalls' => true,
			]
		);
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrorsWithIgnoreAssignmentsInsideFunctionCalls(): void
	{
		$resultFile = self::checkFile(
			__DIR__ . '/data/allAssignmentsInConditions.php',
			[
				'ignoreAssignmentsInsideFunctionCalls' => true,
			]
		);

		self::assertEquals(7, $resultFile->getErrorCount());
		foreach (range(3, 8) as $lineNumber) {
			self::assertSniffError($resultFile, $lineNumber, AssignmentInConditionSniff::CODE_ASSIGNMENT_IN_CONDITION);
		}
	}

	public function testLiveCoding(): void
	{
		$resultFile = self::checkFile(__DIR__ . '/data/assignmentsInConditionsLiveCoding.php');
		self::assertNoSniffErrorInFile($resultFile);
	}

}
