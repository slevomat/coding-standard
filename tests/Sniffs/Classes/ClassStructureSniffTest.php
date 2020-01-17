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
		'destructor',
	];

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/classStructureSniffNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$file = self::checkFile(__DIR__ . '/data/classStructureSniffErrors.php');

		self::assertSame(21, $file->getErrorCount());

		self::assertSniffError($file, 6, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($file, 12, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($file, 18, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($file, 24, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($file, 33, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($file, 44, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($file, 55, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($file, 66, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($file, 77, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($file, 88, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($file, 99, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($file, 111, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($file, 117, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($file, 128, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($file, 132, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($file, 145, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($file, 154, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($file, 161, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($file, 175, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);

		self::assertAllFixedInFile($file);
	}

	public function testManyErrors(): void
	{
		$file = self::checkFile(__DIR__ . '/data/classStructureSniffManyErrors.php');

		self::assertSame(1, $file->getErrorCount());
		self::assertAllFixedInFile($file);
	}

	public function testNoErrorsWithDifferentRules(): void
	{
		$file = self::checkFile(
			__DIR__ . '/data/classStructureSniffNoErrorsWithDifferentRules.php',
			['groups' => self::DIFFERENT_RULES]
		);

		self::assertNoSniffErrorInFile($file);
	}

	public function testErrorsWithDifferentRules(): void
	{
		$file = self::checkFile(
			__DIR__ . '/data/classStructureSniffErrorsWithDifferentRules.php',
			['groups' => self::DIFFERENT_RULES]
		);

		self::assertSame(13, $file->getErrorCount());

		self::assertSniffError($file, 6, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($file, 12, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($file, 27, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($file, 38, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($file, 49, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($file, 60, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($file, 69, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($file, 73, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($file, 85, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($file, 94, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($file, 103, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($file, 107, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);

		self::assertAllFixedInFile($file);
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
