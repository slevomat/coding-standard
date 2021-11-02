<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use SlevomatCodingStandard\Sniffs\TestCase;

class UseFromSameNamespaceSniffTest extends TestCase
{

	public function test(): void
	{
		$report = self::checkFile(__DIR__ . '/data/useFromSameNamespace.php');

		// Unrelated namespaces
		self::assertNoSniffError($report, 5);
		self::assertNoSniffError($report, 6);
		self::assertNoSniffError($report, 10);

		// Use with "as" part
		self::assertNoSniffError($report, 7);

		// Use from same namespace
		self::assertSniffError($report, 8, UseFromSameNamespaceSniff::CODE_USE_FROM_SAME_NAMESPACE, 'Lorem\Ipsum\Dolor');

		// Use from subnamespace
		self::assertNoSniffError($report, 9);

		// Skip closure
		self::assertNoSniffError($report, 12);

		// Nearest previous namespace with multiple namespaces in file
		self::assertSniffError($report, 18, UseFromSameNamespaceSniff::CODE_USE_FROM_SAME_NAMESPACE, 'Bar\Baz\Test');
		self::assertNoSniffError($report, 19);
	}

	public function testFixableUseFromSameNamespace(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/fixableUseFromSameNamespace.php',
			[],
			[UseFromSameNamespaceSniff::CODE_USE_FROM_SAME_NAMESPACE]
		);
		self::assertAllFixedInFile($report);
	}

	public function testUseFromRootNamespaceInFileWithoutNamespace(): void
	{
		$report = self::checkFile(__DIR__ . '/data/useFromRootNamespaceWithoutNamespace.php');

		self::assertSniffError($report, 3, UseFromSameNamespaceSniff::CODE_USE_FROM_SAME_NAMESPACE, 'Foo');
		self::assertNoSniffError($report, 4);
	}

}
