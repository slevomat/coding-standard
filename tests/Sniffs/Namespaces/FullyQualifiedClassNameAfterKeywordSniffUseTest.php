<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

class FullyQualifiedClassNameAfterKeywordSniffUseTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	protected function getSniffClassName(): string
	{
		return FullyQualifiedClassNameAfterKeywordSniff::class;
	}

	private function getFileReport(): \PHP_CodeSniffer\Files\File
	{
		return $this->checkFile(
			__DIR__ . '/data/fullyQualifiedUse.php',
			['keywordsToCheck' => ['T_USE']]
		);
	}

	public function testIgnoreUseInClosure(): void
	{
		$this->assertNoSniffError($this->getFileReport(), 34);
	}

	public function testIgnoreUseInNamespace(): void
	{
		$this->assertNoSniffError($this->getFileReport(), 5);
	}

	public function testIgnoreUseInNamespaceWithParenthesis(): void
	{
		$this->assertNoSniffErrorInFile($this->checkFile(
			__DIR__ . '/data/fullyQualifiedUseNamespaceWithParenthesis.php',
			['keywordsToCheck' => ['T_USE']]
		));
	}

	public function testNonFullyQualifiedUse(): void
	{
		$this->assertSniffError(
			$this->getFileReport(),
			14,
			FullyQualifiedClassNameAfterKeywordSniff::getErrorCode('use'),
			'Dolor in use'
		);
	}

	public function testFullyQualifiedUse(): void
	{
		$this->assertNoSniffError($this->getFileReport(), 9);
	}

	public function testMultipleFullyQualifiedUse(): void
	{
		$this->assertNoSniffError($this->getFileReport(), 19);
	}

	public function testMultipleUseWithFirstWrong(): void
	{
		$this->assertSniffError(
			$this->getFileReport(),
			24,
			FullyQualifiedClassNameAfterKeywordSniff::getErrorCode('use'),
			'Dolor in use'
		);
	}

	public function testMultipleUseWithSecondAndThirdWrong(): void
	{
		$report = $this->getFileReport();
		$this->assertSniffError(
			$report,
			29,
			FullyQualifiedClassNameAfterKeywordSniff::getErrorCode('use'),
			'Amet in use'
		);
		$this->assertSniffError(
			$report,
			29,
			FullyQualifiedClassNameAfterKeywordSniff::getErrorCode('use'),
			'Omega in use'
		);
	}

}
