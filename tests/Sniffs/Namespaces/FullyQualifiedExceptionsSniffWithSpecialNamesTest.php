<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use PHP_CodeSniffer\Files\File;
use SlevomatCodingStandard\Sniffs\TestCase;

class FullyQualifiedExceptionsSniffWithSpecialNamesTest extends TestCase
{

	public function testThrowingUsedException(): void
	{
		self::assertSniffError(
			$this->getFileReport(),
			12,
			FullyQualifiedExceptionsSniff::CODE_NON_FULLY_QUALIFIED_EXCEPTION,
			'FooError'
		);
	}

	public function testCatchingPartiallyUsedException(): void
	{
		self::assertSniffError(
			$this->getFileReport(),
			13,
			FullyQualifiedExceptionsSniff::CODE_NON_FULLY_QUALIFIED_EXCEPTION,
			'Foo\SomeException'
		);
	}

	public function testThrowingExceptionFromSameNamespace(): void
	{
		self::assertSniffError(
			$this->getFileReport(),
			18,
			FullyQualifiedExceptionsSniff::CODE_NON_FULLY_QUALIFIED_EXCEPTION,
			'SomeOtherError'
		);
	}

	public function testCatchingFullyQualifiedException(): void
	{
		self::assertNoSniffError($this->getFileReport(), 15);
	}

	public function testCatchingFullyQualifiedExceptionFromSameNamespace(): void
	{
		self::assertNoSniffError($this->getFileReport(), 17);
	}

	protected static function getSniffClassName(): string
	{
		return FullyQualifiedExceptionsSniff::class;
	}

	private function getFileReport(): File
	{
		return self::checkFile(
			__DIR__ . '/data/fullyQualifiedSpecialExceptionNames.php',
			[
				'specialExceptionNames' => [
					'Foo\SomeError',
					'Lorem\Ipsum\SomeOtherError',
				],
			]
		);
	}

}
