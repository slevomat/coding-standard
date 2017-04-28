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

	public function testIgnoreUseInClosure()
	{
		$this->assertNoSniffError($this->getFileReport(), 34);
	}

	public function testIgnoreUseInNamespace()
	{
		$this->assertNoSniffError($this->getFileReport(), 5);
	}

	public function testIgnoreUseInNamespaceWithParenthesis()
	{
		$this->assertNoSniffErrorInFile($this->checkFile(
			__DIR__ . '/data/fullyQualifiedUseNamespaceWithParenthesis.php',
			['keywordsToCheck' => ['T_USE']]
		));
	}

	public function testNonFullyQualifiedUse()
	{
		$this->assertSniffError(
			$this->getFileReport(),
			14,
			FullyQualifiedClassNameAfterKeywordSniff::getErrorCode('use'),
			'Dolor in use'
		);
	}

	public function testFullyQualifiedUse()
	{
		$this->assertNoSniffError($this->getFileReport(), 9);
	}

	public function testMultipleFullyQualifiedUse()
	{
		$this->assertNoSniffError($this->getFileReport(), 19);
	}

	public function testMultipleUseWithFirstWrong()
	{
		$this->assertSniffError(
			$this->getFileReport(),
			24,
			FullyQualifiedClassNameAfterKeywordSniff::getErrorCode('use'),
			'Dolor in use'
		);
	}

	public function testMultipleUseWithSecondAndThirdWrong()
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
