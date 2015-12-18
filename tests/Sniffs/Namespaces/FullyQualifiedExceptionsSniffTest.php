<?php

namespace SlevomatCodingStandard\Sniffs\Namespaces;

class FullyQualifiedExceptionsSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	private function getFileReport()
	{
		return $this->checkFile(__DIR__ . '/data/fullyQualifiedExceptionNames.php');
	}

	public function testNonFullyQualifiedExceptionInTypehint()
	{
		$this->assertSniffError(
			$this->getFileReport(),
			3,
			FullyQualifiedExceptionsSniff::CODE_NON_FULLY_QUALIFIED_EXCEPTION,
			'FooException'
		);
	}

	public function testNonFullyQualifiedExceptionInThrow()
	{
		$this->assertSniffError(
			$this->getFileReport(),
			6,
			FullyQualifiedExceptionsSniff::CODE_NON_FULLY_QUALIFIED_EXCEPTION,
			'FooException'
		);
	}

	public function testNonFullyQualifiedExceptionInCatch()
	{
		$this->assertSniffError(
			$this->getFileReport(),
			7,
			FullyQualifiedExceptionsSniff::CODE_NON_FULLY_QUALIFIED_EXCEPTION,
			'BarException'
		);
	}

	public function testFullyQualifiedExceptionInTypehint()
	{
		$this->assertNoSniffError($this->getFileReport(), 12);
	}

	public function testFullyQualifiedExceptionInThrow()
	{
		$this->assertNoSniffError($this->getFileReport(), 15);
	}

	public function testFullyQualifiedExceptionInCatch()
	{
		$this->assertNoSniffError($this->getFileReport(), 16);
	}

}
