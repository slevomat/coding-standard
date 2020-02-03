<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use SlevomatCodingStandard\Sniffs\TestCase;

class ClassStructureSniffTest extends TestCase
{

	private const DIFFERENT_RULES = [
		'uses',
		'public constants, protected constants, private constants',
		'public static properties, protected static properties, private static properties',
		'public static methods, protected static methods, private static methods',
		'public properties, protected properties, private properties',
		'magic methods',
		'public methods, protected methods, private methods',
		'constructor',
		'static constructors',
	];

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/classStructureSniffNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/classStructureSniffErrors.php');

		self::assertSame(25, $report->getErrorCount());

		self::assertSniffError($report, 6, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 12, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 18, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 24, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 33, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 44, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 55, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 66, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 77, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 88, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 99, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 111, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 117, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 128, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 132, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 145, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 154, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 158, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 161, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 175, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 188, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 207, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 209, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);

		self::assertAllFixedInFile($report);
	}

	public function testManyErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/classStructureSniffManyErrors.php');

		self::assertSame(1, $report->getErrorCount());
		self::assertAllFixedInFile($report);
	}

	public function testNoErrorsWithDifferentRules(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/classStructureSniffNoErrorsWithDifferentRules.php',
			['groups' => self::DIFFERENT_RULES]
		);

		self::assertNoSniffErrorInFile($report);
	}

	public function testErrorsWithDifferentRules(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/classStructureSniffErrorsWithDifferentRules.php',
			['groups' => self::DIFFERENT_RULES]
		);

		self::assertSame(13, $report->getErrorCount());

		self::assertSniffError($report, 6, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 12, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 27, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 38, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 49, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 60, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 69, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 73, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 85, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 94, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 103, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 107, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);

		self::assertAllFixedInFile($report);
	}

	public function testThrowExceptionForUnsupportedSection(): void
	{
		try {
			self::checkFile(
				__DIR__ . '/data/classStructureSniffNoErrors.php',
				['groups' => ['whatever']]
			);
			self::fail();
		} catch (UnsupportedClassGroupException $e) {
			self::assertStringContainsString('whatever', $e->getMessage());
		}
	}

}
