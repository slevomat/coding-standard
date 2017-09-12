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

	public function testThrowingUsedException(): void
	{
		$this->assertSniffError(
			$this->getFileReport(),
			12,
			FullyQualifiedExceptionsSniff::CODE_NON_FULLY_QUALIFIED_EXCEPTION,
			'FooError'
		);
	}

	public function testCatchingPartiallyUsedException(): void
	{
		$this->assertSniffError(
			$this->getFileReport(),
			13,
			FullyQualifiedExceptionsSniff::CODE_NON_FULLY_QUALIFIED_EXCEPTION,
			'Foo\SomeException'
		);
	}

	public function testThrowingExceptionFromSameNamespace(): void
	{
		$this->assertSniffError(
			$this->getFileReport(),
			18,
			FullyQualifiedExceptionsSniff::CODE_NON_FULLY_QUALIFIED_EXCEPTION,
			'SomeOtherError'
		);
	}

	public function testCatchingFullyQualifiedException(): void
	{
		$this->assertNoSniffError($this->getFileReport(), 15);
	}


	public function testCatchingFullyQualifiedExceptionFromSameNamespace(): void
	{
		$this->assertNoSniffError($this->getFileReport(), 17);
	}

}
