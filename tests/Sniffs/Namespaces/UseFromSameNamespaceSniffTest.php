<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

class UseFromSameNamespaceSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	private function getFileReport(): \PHP_CodeSniffer\Files\File
	{
		return $this->checkFile(__DIR__ . '/data/useFromSameNamespace.php');
	}

	public function testUnrelatedNamespaces(): void
	{
		$report = $this->getFileReport();
		$this->assertNoSniffError($report, 5);
		$this->assertNoSniffError($report, 6);
		$this->assertNoSniffError($report, 10);
	}

	public function testUseWithAsPart(): void
	{
		$this->assertNoSniffError($this->getFileReport(), 7);
	}

	public function testUseFromSubnamespace(): void
	{
		$this->assertNoSniffError($this->getFileReport(), 9);
	}

	public function testUseFromSameNamespace(): void
	{
		$this->assertSniffError(
			$this->getFileReport(),
			8,
			UseFromSameNamespaceSniff::CODE_USE_FROM_SAME_NAMESPACE,
			'Lorem\Ipsum\Dolor'
		);
	}

	public function testSkipClosure(): void
	{
		$this->assertNoSniffError($this->getFileReport(), 12);
	}

	public function testCheckNearestPreviousNamespaceWithMultipleNamespacesInFile(): void
	{
		$report = $this->getFileReport();
		$this->assertSniffError(
			$report,
			18,
			UseFromSameNamespaceSniff::CODE_USE_FROM_SAME_NAMESPACE,
			'Bar\Baz\Test'
		);
		$this->assertNoSniffError($report, 19);
	}

	public function testFixableUseFromSameNamespace(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/fixableUseFromSameNamespace.php', [], [UseFromSameNamespaceSniff::CODE_USE_FROM_SAME_NAMESPACE]);
		$this->assertAllFixedInFile($report);
	}

}
