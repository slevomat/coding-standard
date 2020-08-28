<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use SlevomatCodingStandard\Sniffs\TestCase;
use function range;

class UnusedPrivateElementsSniffTest extends TestCase
{

	public function testErrors(): void
	{
		$resultFile = self::checkFile(__DIR__ . '/data/classWithSomeUnusedElements.php', [
			'alwaysUsedPropertiesAnnotations' => [
				'@get',
				'@set',
				'@ORM\Column',
				'@Assert\\',
			],
			'alwaysUsedPropertiesSuffixes' => [
				'Value',
				'Timestamp',
			],
			'alwaysUsedMethodsAnnotations' => [
				'@Serializer\\PostDeserialize',
				'@JMS\\Serializer\\Annotation\\',
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
			48,
			UnusedPrivateElementsSniff::CODE_UNUSED_METHOD,
			'Class ClassWithSomeUnusedProperties contains unused private method unusedPrivateMethod().'
		);
		self::assertNoSniffError($resultFile, 51);
		self::assertNoSniffError($resultFile, 58);
		self::assertNoSniffError($resultFile, 63);
		self::assertSniffError(
			$resultFile,
			68,
			UnusedPrivateElementsSniff::CODE_UNUSED_METHOD,
			'Class ClassWithSomeUnusedProperties contains unused private method unusedStaticPrivateMethod().'
		);
		self::assertNoSniffError($resultFile, 73);

		self::assertNoSniffError($resultFile, 81);
		self::assertNoSniffError($resultFile, 86);
		self::assertNoSniffError($resultFile, 91);

		self::assertSniffError(
			$resultFile,
			96,
			UnusedPrivateElementsSniff::CODE_UNUSED_METHOD,
			'Class ClassWithSomeUnusedProperties contains unused private method unusedMethodReturningReference().'
		);

		self::assertSniffError(
			$resultFile,
			101,
			UnusedPrivateElementsSniff::CODE_UNUSED_PROPERTY,
			'Class ClassWithSomeUnusedProperties contains unused property $unusedPropertyWithWeirdDefinition.'
		);
		self::assertSniffError(
			$resultFile,
			103,
			UnusedPrivateElementsSniff::CODE_WRITE_ONLY_PROPERTY,
			'Class ClassWithSomeUnusedProperties contains write-only property $unusedWriteOnlyPropertyWithWeirdDefinition.'
		);

		self::assertNoSniffError($resultFile, 110);

		self::assertSniffError(
			$resultFile,
			120,
			UnusedPrivateElementsSniff::CODE_UNUSED_PROPERTY,
			'Class ClassWithSomeUnusedProperties contains unused property $unusedStaticProperty1.'
		);
		self::assertSniffError(
			$resultFile,
			121,
			UnusedPrivateElementsSniff::CODE_UNUSED_PROPERTY,
			'Class ClassWithSomeUnusedProperties contains unused property $unusedStaticProperty2.'
		);

		self::assertNoSniffError($resultFile, 123);
		self::assertNoSniffError($resultFile, 130);
		self::assertNoSniffError($resultFile, 135);
		self::assertNoSniffError($resultFile, 159);

		self::assertNoSniffError($resultFile, 161);
		self::assertNoSniffError($resultFile, 169);

		self::assertSniffError(
			$resultFile,
			180,
			UnusedPrivateElementsSniff::CODE_UNUSED_PROPERTY,
			'Class ClassWithSomeUnusedProperties contains unused property $propertyWithPrefixUnsetAnnotation.'
		);

		self::assertNoSniffError($resultFile, 182);

		self::assertNoSniffError($resultFile, 190);
		self::assertNoSniffError($resultFile, 192);

		self::assertNoSniffError($resultFile, 205);

		self::assertNoSniffError($resultFile, 210);
	}

	public function testOnlyPublicElements(): void
	{
		$report = self::checkFile(__DIR__ . '/data/classWithOnlyPublicElements.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testClassWithSpecialThis(): void
	{
		$report = self::checkFile(__DIR__ . '/data/classWithSpecialThis.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testClassWithSpecialSelf(): void
	{
		$report = self::checkFile(__DIR__ . '/data/classWithSpecialSelf.php');
		self::assertNoSniffErrorInFile($report);
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

	public function testClassWithTypedProperties(): void
	{
		$resultFile = self::checkFile(__DIR__ . '/data/classWithTypedProperties.php');

		self::assertSame(4, $resultFile->getErrorCount());

		self::assertSniffError($resultFile, 7, UnusedPrivateElementsSniff::CODE_WRITE_ONLY_PROPERTY);
		self::assertSniffError($resultFile, 8, UnusedPrivateElementsSniff::CODE_WRITE_ONLY_PROPERTY);
		self::assertSniffError($resultFile, 9, UnusedPrivateElementsSniff::CODE_WRITE_ONLY_PROPERTY);
		self::assertSniffError($resultFile, 10, UnusedPrivateElementsSniff::CODE_WRITE_ONLY_PROPERTY);
	}

}
