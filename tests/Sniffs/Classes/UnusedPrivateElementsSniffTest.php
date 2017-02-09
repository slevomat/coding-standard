<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

class UnusedPrivateElementsSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testErrors()
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
	}

	public function testOnlyPublicElements()
	{
		$this->assertNoSniffErrorInFile($this->checkFile(__DIR__ . '/data/classWithOnlyPublicElements.php'));
	}

	public function testClassWithSpecialThis()
	{
		$this->assertNoSniffErrorInFile($this->checkFile(__DIR__ . '/data/classWithSpecialThis.php'));
	}

	public function testClassWithSpecialSelf()
	{
		$this->assertNoSniffErrorInFile($this->checkFile(__DIR__ . '/data/classWithSpecialSelf.php'));
	}

}
