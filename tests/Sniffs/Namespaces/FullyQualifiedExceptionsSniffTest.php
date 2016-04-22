<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

class FullyQualifiedExceptionsSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	private function getFileReport(): \PHP_CodeSniffer_File
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
		$this->assertSniffError(
			$this->getFileReport(),
			9,
			FullyQualifiedExceptionsSniff::CODE_NON_FULLY_QUALIFIED_EXCEPTION,
			'Throwable'
		);
		$this->assertSniffError(
			$this->getFileReport(),
			11,
			FullyQualifiedExceptionsSniff::CODE_NON_FULLY_QUALIFIED_EXCEPTION,
			'TypeError'
		);
	}

	public function testFullyQualifiedExceptionInTypehint()
	{
		$this->assertNoSniffError($this->getFileReport(), 16);
	}

	public function testFullyQualifiedExceptionInThrow()
	{
		$this->assertNoSniffError($this->getFileReport(), 19);
	}

	public function testFullyQualifiedExceptionInCatch()
	{
		$this->assertNoSniffError($this->getFileReport(), 20);
		$this->assertNoSniffError($this->getFileReport(), 22);
		$this->assertNoSniffError($this->getFileReport(), 24);
		$this->assertNoSniffError($this->getFileReport(), 26);
		$this->assertNoSniffError($this->getFileReport(), 28);
	}

}
