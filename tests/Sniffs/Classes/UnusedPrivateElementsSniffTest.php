<?php

namespace SlevomatCodingStandard\Sniffs\Classes;

class UnusedPrivateElementsSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testCheckFile()
	{
		$resultFile = $this->checkFile(__DIR__ . '/data/ClassWithSomeUnusedProperties.php', [
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
			'Class ClassWithSomeUnusedProperties contains unused property: $unusedProperty'
		);
		$this->assertSniffError(
			$resultFile,
			28,
			UnusedPrivateElementsSniff::CODE_UNUSED_PROPERTY,
			'Class ClassWithSomeUnusedProperties contains unused property: $unusedPropertyWhichNameIsAlsoAFunction'
		);
		$this->assertSniffError(
			$resultFile,
			30,
			UnusedPrivateElementsSniff::CODE_WRITE_ONLY_PROPERTY,
			'Class ClassWithSomeUnusedProperties contains write-only property: $writeOnlyProperty'
		);
		$this->assertNoSniffError($resultFile, 33);
		$this->assertNoSniffError($resultFile, 35);
		$this->assertSniffError(
			$resultFile,
			51,
			UnusedPrivateElementsSniff::CODE_UNUSED_METHOD,
			'Class ClassWithSomeUnusedProperties contains unused private method: unusedPrivateMethod'
		);
		$this->assertNoSniffError($resultFile, 54);
		$this->assertNoSniffError($resultFile, 61);
		$this->assertNoSniffError($resultFile, 66);
		$this->assertSniffError(
			$resultFile,
			71,
			UnusedPrivateElementsSniff::CODE_UNUSED_METHOD,
			'Class ClassWithSomeUnusedProperties contains unused private method: unusedStaticPrivateMethod'
		);
		$this->assertNoSniffError($resultFile, 76);
	}

}
