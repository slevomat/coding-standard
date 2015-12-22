<?php

namespace SlevomatCodingStandard\Sniffs\Classes;

class UnusedPrivateElementsSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testCheckFile()
	{
		$resultFile = $this->checkFile(__DIR__ . '/data/ClassWithSomeUnusedProperties.php');
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
		$this->assertSniffError(
			$resultFile,
			45,
			UnusedPrivateElementsSniff::CODE_UNUSED_METHOD,
			'Class ClassWithSomeUnusedProperties contains unused private method: unusedPrivateMethod'
		);
		$this->assertNoSniffError($resultFile, 50);
		$this->assertNoSniffError($resultFile, 55);
		$this->assertNoSniffError($resultFile, 60);
		$this->assertSniffError(
			$resultFile,
			65,
			UnusedPrivateElementsSniff::CODE_UNUSED_METHOD,
			'Class ClassWithSomeUnusedProperties contains unused private method: unusedStaticPrivateMethod'
		);
		$this->assertNoSniffError($resultFile, 70);
	}

}
