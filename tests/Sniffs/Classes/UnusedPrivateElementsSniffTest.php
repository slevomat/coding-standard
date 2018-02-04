<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

class UnusedPrivateElementsSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testErrors(): void
	{
		$resultFile = self::checkFile(__DIR__ . '/data/classWithSomeUnusedElements.php', [
			'alwaysUsedPropertiesAnnotations' => [
				'@get',
				'@set',
				'@ORM\Column',
			],
			'alwaysUsedPropertiesSuffixes' => [
				'Value',
				'Timestamp',
			],
		]);
		self::assertNoSniffError($resultFile, 6);
		self::assertNoSniffError($resultFile, 8);
		self::assertNoSniffError($resultFile, 10);
		self::assertNoSniffError($resultFile, 15);
		self::assertNoSniffError($resultFile, 20);
		self::assertNoSniffError($resultFile, 22);
		self::assertNoSniffError($resultFile, 24);
		self::assertSniffError(
			$resultFile,
			26,
			UnusedPrivateElementsSniff::CODE_UNUSED_PROPERTY,
			'Class ClassWithSomeUnusedProperties contains unused property $unusedProperty.'
		);
		self::assertSniffError(
			$resultFile,
			28,
			UnusedPrivateElementsSniff::CODE_UNUSED_PROPERTY,
			'Class ClassWithSomeUnusedProperties contains unused property $unusedPropertyWhichNameIsAlsoAFunction.'
		);
		self::assertSniffError(
			$resultFile,
			30,
			UnusedPrivateElementsSniff::CODE_WRITE_ONLY_PROPERTY,
			'Class ClassWithSomeUnusedProperties contains write-only property $writeOnlyProperty.'
		);
		self::assertNoSniffError($resultFile, 33);
		self::assertSniffError(
			$resultFile,
			51,
			UnusedPrivateElementsSniff::CODE_UNUSED_METHOD,
			'Class ClassWithSomeUnusedProperties contains unused private method unusedPrivateMethod().'
		);
		self::assertNoSniffError($resultFile, 54);
		self::assertNoSniffError($resultFile, 61);
		self::assertNoSniffError($resultFile, 66);
		self::assertSniffError(
			$resultFile,
			71,
			UnusedPrivateElementsSniff::CODE_UNUSED_METHOD,
			'Class ClassWithSomeUnusedProperties contains unused private method unusedStaticPrivateMethod().'
		);
		self::assertNoSniffError($resultFile, 76);

		self::assertNoSniffError($resultFile, 84);
		self::assertNoSniffError($resultFile, 89);
		self::assertNoSniffError($resultFile, 94);

		self::assertSniffError(
			$resultFile,
			99,
			UnusedPrivateElementsSniff::CODE_UNUSED_METHOD,
			'Class ClassWithSomeUnusedProperties contains unused private method unusedMethodReturningReference().'
		);

		self::assertSniffError(
			$resultFile,
			104,
			UnusedPrivateElementsSniff::CODE_UNUSED_PROPERTY,
			'Class ClassWithSomeUnusedProperties contains unused property $unusedPropertyWithWeirdDefinition.'
		);
		self::assertSniffError(
			$resultFile,
			106,
			UnusedPrivateElementsSniff::CODE_WRITE_ONLY_PROPERTY,
			'Class ClassWithSomeUnusedProperties contains write-only property $unusedWriteOnlyPropertyWithWeirdDefinition.'
		);

		self::assertNoSniffError($resultFile, 113);

		self::assertSniffError(
			$resultFile,
			123,
			UnusedPrivateElementsSniff::CODE_UNUSED_PROPERTY,
			'Class ClassWithSomeUnusedProperties contains unused property $unusedStaticProperty1.'
		);
		self::assertSniffError(
			$resultFile,
			124,
			UnusedPrivateElementsSniff::CODE_UNUSED_PROPERTY,
			'Class ClassWithSomeUnusedProperties contains unused property $unusedStaticProperty2.'
		);

		self::assertNoSniffError($resultFile, 126);

		self::assertNoSniffError($resultFile, 133);
	}

	public function testOnlyPublicElements(): void
	{
		self::assertNoSniffErrorInFile(self::checkFile(__DIR__ . '/data/classWithOnlyPublicElements.php'));
	}

	public function testClassWithSpecialThis(): void
	{
		self::assertNoSniffErrorInFile(self::checkFile(__DIR__ . '/data/classWithSpecialThis.php'));
	}

	public function testClassWithSpecialSelf(): void
	{
		self::assertNoSniffErrorInFile(self::checkFile(__DIR__ . '/data/classWithSpecialSelf.php'));
	}

	public function testClassWithPrivateElementsUsedOnSelfInstance(): void
	{
		$report = self::checkFile(__DIR__ . '/data/classWithPrivateElementsUsedOnSelfInstance.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testClassWithPrivateElementsUsedOnStaticInstance(): void
	{
		$report = self::checkFile(__DIR__ . '/data/classWithPrivateElementsUsedOnStaticInstance.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testClassWithPrivateElementsUsedOnSameClass(): void
	{
		$report = self::checkFile(__DIR__ . '/data/classWithPrivateElementsUsedOnSameClass.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testClassWithChainedPrivateMethods(): void
	{
		$report = self::checkFile(__DIR__ . '/data/classWithChainedPrivateMethods.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testClassWithConstants(): void
	{
		$resultFile = self::checkFile(__DIR__ . '/data/classWithConstants.php');

		self::assertSame(1, $resultFile->getErrorCount());

		self::assertNoSniffError($resultFile, 6);
		self::assertNoSniffError($resultFile, 7);
		self::assertNoSniffError($resultFile, 9);
		self::assertNoSniffError($resultFile, 11);
		self::assertNoSniffError($resultFile, 12);

		self::assertSniffError(
			$resultFile,
			10,
			UnusedPrivateElementsSniff::CODE_UNUSED_CONSTANT,
			'Class ClassWithConstants contains unused private constant PRIVATE_FOO.'
		);
	}

	public function testClassWithPropertyUsedInString(): void
	{
		$resultFile = self::checkFile(__DIR__ . '/data/classWithPropertyUsedInString.php');

		self::assertSame(1, $resultFile->getErrorCount());

		self::assertSniffError(
			$resultFile,
			8,
			UnusedPrivateElementsSniff::CODE_WRITE_ONLY_PROPERTY,
			'Class Foo contains write-only property $hoo.'
		);
	}

	public function testClassWithMethodUsedInString(): void
	{
		$resultFile = self::checkFile(__DIR__ . '/data/classWithMethodUsedInString.php');

		self::assertSame(1, $resultFile->getErrorCount());

		self::assertSniffError(
			$resultFile,
			10,
			UnusedPrivateElementsSniff::CODE_UNUSED_METHOD,
			'Class Foo contains unused private method foo().'
		);
	}

	public function testClassWithWriteOnlyProperties(): void
	{
		$resultFile = self::checkFile(__DIR__ . '/data/classWithWriteOnlyProperties.php');

		self::assertSame(13, $resultFile->getErrorCount());

		foreach (range(6, 18) as $line) {
			self::assertSniffError($resultFile, $line, UnusedPrivateElementsSniff::CODE_WRITE_ONLY_PROPERTY);
		}
	}

}
