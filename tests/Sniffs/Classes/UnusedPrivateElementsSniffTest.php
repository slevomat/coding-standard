<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

class UnusedPrivateElementsSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testErrors(): void
	{
		$resultFile = $this->checkFile(__DIR__ . '/data/classWithSomeUnusedElements.php', [
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
		$this->assertNoSniffError($resultFile, 6);
		$this->assertNoSniffError($resultFile, 8);
		$this->assertNoSniffError($resultFile, 10);
		$this->assertNoSniffError($resultFile, 15);
		$this->assertNoSniffError($resultFile, 20);
		$this->assertNoSniffError($resultFile, 22);
		$this->assertNoSniffError($resultFile, 24);
		$this->assertSniffError(
			$resultFile,
			26,
			UnusedPrivateElementsSniff::CODE_UNUSED_PROPERTY,
			'Class ClassWithSomeUnusedProperties contains unused property $unusedProperty.'
		);
		$this->assertSniffError(
			$resultFile,
			28,
			UnusedPrivateElementsSniff::CODE_UNUSED_PROPERTY,
			'Class ClassWithSomeUnusedProperties contains unused property $unusedPropertyWhichNameIsAlsoAFunction.'
		);
		$this->assertSniffError(
			$resultFile,
			30,
			UnusedPrivateElementsSniff::CODE_WRITE_ONLY_PROPERTY,
			'Class ClassWithSomeUnusedProperties contains write-only property $writeOnlyProperty.'
		);
		$this->assertNoSniffError($resultFile, 33);
		$this->assertSniffError(
			$resultFile,
			48,
			UnusedPrivateElementsSniff::CODE_UNUSED_METHOD,
			'Class ClassWithSomeUnusedProperties contains unused private method unusedPrivateMethod().'
		);
		$this->assertNoSniffError($resultFile, 51);
		$this->assertNoSniffError($resultFile, 58);
		$this->assertNoSniffError($resultFile, 63);
		$this->assertSniffError(
			$resultFile,
			68,
			UnusedPrivateElementsSniff::CODE_UNUSED_METHOD,
			'Class ClassWithSomeUnusedProperties contains unused private method unusedStaticPrivateMethod().'
		);
		$this->assertNoSniffError($resultFile, 73);

		$this->assertNoSniffError($resultFile, 81);
		$this->assertNoSniffError($resultFile, 86);
		$this->assertNoSniffError($resultFile, 91);

		$this->assertSniffError(
			$resultFile,
			96,
			UnusedPrivateElementsSniff::CODE_UNUSED_METHOD,
			'Class ClassWithSomeUnusedProperties contains unused private method unusedMethodReturningReference().'
		);

		$this->assertSniffError(
			$resultFile,
			101,
			UnusedPrivateElementsSniff::CODE_UNUSED_PROPERTY,
			'Class ClassWithSomeUnusedProperties contains unused property $unusedPropertyWithWeirdDefinition.'
		);
		$this->assertSniffError(
			$resultFile,
			103,
			UnusedPrivateElementsSniff::CODE_WRITE_ONLY_PROPERTY,
			'Class ClassWithSomeUnusedProperties contains write-only property $unusedWriteOnlyPropertyWithWeirdDefinition.'
		);

		$this->assertNoSniffError($resultFile, 110);
	}

	public function testOnlyPublicElements(): void
	{
		$this->assertNoSniffErrorInFile($this->checkFile(__DIR__ . '/data/classWithOnlyPublicElements.php'));
	}

	public function testClassWithSpecialThis(): void
	{
		$this->assertNoSniffErrorInFile($this->checkFile(__DIR__ . '/data/classWithSpecialThis.php'));
	}

	public function testClassWithSpecialSelf(): void
	{
		$this->assertNoSniffErrorInFile($this->checkFile(__DIR__ . '/data/classWithSpecialSelf.php'));
	}

	public function testClassWithPrivateElementsUsedOnSelfInstance(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/classWithPrivateElementsUsedOnSelfInstance.php');
		$this->assertNoSniffErrorInFile($report);
	}

	public function testClassWithPrivateElementsUsedOnStaticInstance(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/classWithPrivateElementsUsedOnStaticInstance.php');
		$this->assertNoSniffErrorInFile($report);
	}

	public function testClassWithChainedPrivateMethods(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/classWithChainedPrivateMethods.php');
		$this->assertNoSniffErrorInFile($report);
	}

	public function testClassWithConstants(): void
	{
		$resultFile = $this->checkFile(__DIR__ . '/data/classWithConstants.php');

		$this->assertSame(1, $resultFile->getErrorCount());

		$this->assertNoSniffError($resultFile, 6);
		$this->assertNoSniffError($resultFile, 7);
		$this->assertNoSniffError($resultFile, 9);
		$this->assertNoSniffError($resultFile, 11);
		$this->assertNoSniffError($resultFile, 12);

		$this->assertSniffError(
			$resultFile,
			10,
			UnusedPrivateElementsSniff::CODE_UNUSED_CONSTANT,
			'Class ClassWithConstants contains unused private constant PRIVATE_FOO.'
		);
	}

	public function testClassWithPropertyUsedInString(): void
	{
		$resultFile = $this->checkFile(__DIR__ . '/data/classWithPropertyUsedInString.php');

		$this->assertSame(1, $resultFile->getErrorCount());

		$this->assertSniffError(
			$resultFile,
			8,
			UnusedPrivateElementsSniff::CODE_WRITE_ONLY_PROPERTY,
			'Class Foo contains write-only property $hoo.'
		);
	}

	public function testClassWithMethodUsedInString(): void
	{
		$resultFile = $this->checkFile(__DIR__ . '/data/classWithMethodUsedInString.php');

		$this->assertSame(1, $resultFile->getErrorCount());

		$this->assertSniffError(
			$resultFile,
			10,
			UnusedPrivateElementsSniff::CODE_UNUSED_METHOD,
			'Class Foo contains unused private method foo().'
		);
	}

}
