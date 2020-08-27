<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use PHP_CodeSniffer\Files\File;
use SlevomatCodingStandard\Sniffs\TestCase;

class UseFromSameNamespaceSniffTest extends TestCase
{

	public function testUnrelatedNamespaces(): void
	{
		$report = $this->getFileReport();
		self::assertNoSniffError($report, 5);
		self::assertNoSniffError($report, 6);
		self::assertNoSniffError($report, 10);
	}

	public function testUseWithAsPart(): void
	{
		self::assertNoSniffError($this->getFileReport(), 7);
	}

	public function testUseFromSubnamespace(): void
	{
		self::assertNoSniffError($this->getFileReport(), 9);
	}

	public function testUseFromSameNamespace(): void
	{
		self::assertSniffError(
			$this->getFileReport(),
			8,
			UseFromSameNamespaceSniff::CODE_USE_FROM_SAME_NAMESPACE,
			'Lorem\Ipsum\Dolor'
		);
	}

	public function testSkipClosure(): void
	{
		self::assertNoSniffError($this->getFileReport(), 12);
	}

	public function testCheckNearestPreviousNamespaceWithMultipleNamespacesInFile(): void
	{
		$report = $this->getFileReport();
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

	private function getFileReport(): File
	{
		return self::checkFile(__DIR__ . '/data/useFromSameNamespace.php');
	}

}
