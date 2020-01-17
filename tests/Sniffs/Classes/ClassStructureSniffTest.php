<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use SlevomatCodingStandard\Sniffs\TestCase;

class ClassStructureSniffTest extends TestCase
{

	private const DIFFERENT_RULES = [
		ClassStructureSniff::GROUP_NONE => 0,
		ClassStructureSniff::GROUP_USES => 10,
		ClassStructureSniff::GROUP_PUBLIC_CONSTANTS => 20,
		ClassStructureSniff::GROUP_PROTECTED_CONSTANTS => 20,
		ClassStructureSniff::GROUP_PRIVATE_CONSTANTS => 20,
		ClassStructureSniff::GROUP_PUBLIC_STATIC_PROPERTIES => 30,
		ClassStructureSniff::GROUP_PROTECTED_STATIC_PROPERTIES => 30,
		ClassStructureSniff::GROUP_PRIVATE_STATIC_PROPERTIES => 30,
		ClassStructureSniff::GROUP_PUBLIC_STATIC_METHODS => 40,
		ClassStructureSniff::GROUP_PROTECTED_STATIC_METHODS => 40,
		ClassStructureSniff::GROUP_PRIVATE_STATIC_METHODS => 40,
		ClassStructureSniff::GROUP_PUBLIC_PROPERTIES => 50,
		ClassStructureSniff::GROUP_PROTECTED_PROPERTIES => 50,
		ClassStructureSniff::GROUP_PRIVATE_PROPERTIES => 50,
		ClassStructureSniff::GROUP_MAGIC_METHODS => 60,
		ClassStructureSniff::GROUP_PUBLIC_METHODS => 70,
		ClassStructureSniff::GROUP_PROTECTED_METHODS => 70,
		ClassStructureSniff::GROUP_PRIVATE_METHODS => 70,
		ClassStructureSniff::GROUP_CONSTRUCTOR => 80,
		ClassStructureSniff::GROUP_STATIC_CONSTRUCTORS => 90,
		ClassStructureSniff::GROUP_DESTRUCTOR => 100,
	];

	public function testNoErrors(): void
	{
		self::assertNoSniffErrorInFile(self::checkFile(__DIR__ . '/data/classStructureSniffNoErrors.php'));
	}

	public function testErrors(): void
	{
		$file = self::checkFile(__DIR__ . '/data/classStructureSniffErrors.php');

		self::assertSame(21, $file->getErrorCount());

		self::assertSniffError($file, 6, ClassStructureSniff::CODE_INVALID_GROUP_ORDER);
		self::assertSniffError($file, 12, ClassStructureSniff::CODE_INVALID_GROUP_ORDER);
		self::assertSniffError($file, 18, ClassStructureSniff::CODE_INVALID_GROUP_ORDER);
		self::assertSniffError($file, 24, ClassStructureSniff::CODE_INVALID_GROUP_ORDER);
		self::assertSniffError($file, 33, ClassStructureSniff::CODE_INVALID_GROUP_ORDER);
		self::assertSniffError($file, 44, ClassStructureSniff::CODE_INVALID_GROUP_ORDER);
		self::assertSniffError($file, 55, ClassStructureSniff::CODE_INVALID_GROUP_ORDER);
		self::assertSniffError($file, 66, ClassStructureSniff::CODE_INVALID_GROUP_ORDER);
		self::assertSniffError($file, 77, ClassStructureSniff::CODE_INVALID_GROUP_ORDER);
		self::assertSniffError($file, 88, ClassStructureSniff::CODE_INVALID_GROUP_ORDER);
		self::assertSniffError($file, 99, ClassStructureSniff::CODE_INVALID_GROUP_ORDER);
		self::assertSniffError($file, 111, ClassStructureSniff::CODE_INVALID_GROUP_ORDER);
		self::assertSniffError($file, 117, ClassStructureSniff::CODE_INVALID_GROUP_ORDER);
		self::assertSniffError($file, 128, ClassStructureSniff::CODE_INVALID_GROUP_ORDER);
		self::assertSniffError($file, 132, ClassStructureSniff::CODE_INVALID_GROUP_ORDER);
		self::assertSniffError($file, 145, ClassStructureSniff::CODE_INVALID_GROUP_ORDER);
		self::assertSniffError($file, 154, ClassStructureSniff::CODE_INVALID_GROUP_ORDER);
		self::assertSniffError($file, 161, ClassStructureSniff::CODE_INVALID_GROUP_ORDER);
		self::assertSniffError($file, 175, ClassStructureSniff::CODE_INVALID_GROUP_ORDER);

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
			['requiredOrder' => self::DIFFERENT_RULES]
		);

		self::assertNoSniffErrorInFile($file);
	}

	public function testErrorsWithDifferentRules(): void
	{
		$file = self::checkFile(
			__DIR__ . '/data/classStructureSniffErrorsWithDifferentRules.php',
			['requiredOrder' => self::DIFFERENT_RULES]
		);

		self::assertSame(13, $file->getErrorCount());

		self::assertSniffError($file, 6, ClassStructureSniff::CODE_INVALID_GROUP_ORDER);
		self::assertSniffError($file, 12, ClassStructureSniff::CODE_INVALID_GROUP_ORDER);
		self::assertSniffError($file, 27, ClassStructureSniff::CODE_INVALID_GROUP_ORDER);
		self::assertSniffError($file, 38, ClassStructureSniff::CODE_INVALID_GROUP_ORDER);
		self::assertSniffError($file, 49, ClassStructureSniff::CODE_INVALID_GROUP_ORDER);
		self::assertSniffError($file, 60, ClassStructureSniff::CODE_INVALID_GROUP_ORDER);
		self::assertSniffError($file, 69, ClassStructureSniff::CODE_INVALID_GROUP_ORDER);
		self::assertSniffError($file, 73, ClassStructureSniff::CODE_INVALID_GROUP_ORDER);
		self::assertSniffError($file, 85, ClassStructureSniff::CODE_INVALID_GROUP_ORDER);
		self::assertSniffError($file, 94, ClassStructureSniff::CODE_INVALID_GROUP_ORDER);
		self::assertSniffError($file, 103, ClassStructureSniff::CODE_INVALID_GROUP_ORDER);
		self::assertSniffError($file, 107, ClassStructureSniff::CODE_INVALID_GROUP_ORDER);

		self::assertAllFixedInFile($file);
	}

}
