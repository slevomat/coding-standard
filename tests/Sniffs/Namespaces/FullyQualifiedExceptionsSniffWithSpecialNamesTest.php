<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

class FullyQualifiedExceptionsSniffWithSpecialNamesTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	protected function getSniffClassName(): string
	{
		return FullyQualifiedExceptionsSniff::class;
	}

	private function getFileReport(): \PHP_CodeSniffer\Files\File
	{
		return $this->checkFile(
			__DIR__ . '/data/fullyQualifiedSpecialExceptionNames.php',
			['specialExceptionNames' => [
				'Foo\SomeError',
				'Lorem\Ipsum\SomeOtherError',
			]]
		);
	}

	public function testThrowingUsedException()
	{
		$this->assertSniffError(
			$this->getFileReport(),
			12,
			FullyQualifiedExceptionsSniff::CODE_NON_FULLY_QUALIFIED_EXCEPTION,
			'FooError'
		);
	}

	public function testCatchingPartiallyUsedException()
	{
		$this->assertSniffError(
			$this->getFileReport(),
			13,
			FullyQualifiedExceptionsSniff::CODE_NON_FULLY_QUALIFIED_EXCEPTION,
			'Foo\SomeException'
		);
	}

	public function testThrowingExceptionFromSameNamespace()
	{
		$this->assertSniffError(
			$this->getFileReport(),
			18,
			FullyQualifiedExceptionsSniff::CODE_NON_FULLY_QUALIFIED_EXCEPTION,
			'SomeOtherError'
		);
	}

	public function testCatchingFullyQualifiedException()
	{
		$this->assertNoSniffError($this->getFileReport(), 15);
	}


	public function testCatchingFullyQualifiedExceptionFromSameNamespace()
	{
		$this->assertNoSniffError($this->getFileReport(), 17);
	}

}
