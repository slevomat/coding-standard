<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

class FullyQualifiedClassNameAfterKeywordSniffImplementsTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	protected function getSniffClassName(): string
	{
		return FullyQualifiedClassNameAfterKeywordSniff::class;
	}

	private function getFileReport(): \PHP_CodeSniffer\Files\File
	{
		return $this->checkFile(
			__DIR__ . '/data/fullyQualifiedImplements.php',
			['keywordsToCheck' => ['T_IMPLEMENTS']]
		);
	}

	public function testNonFullyQualifiedImplements()
	{
		$this->assertSniffError(
			$this->getFileReport(),
			8,
			FullyQualifiedClassNameAfterKeywordSniff::getErrorCode('implements'),
			'Dolor in implements'
		);
	}

	public function testFullyQualifiedImplements()
	{
		$this->assertNoSniffError($this->getFileReport(), 3);
	}

	public function testMultipleFullyQualifiedImplements()
	{
		$this->assertNoSniffError($this->getFileReport(), 13);
	}

	public function testMultipleImplementsWithFirstWrong()
	{
		$this->assertSniffError(
			$this->getFileReport(),
			18,
			FullyQualifiedClassNameAfterKeywordSniff::getErrorCode('implements'),
			'Dolor in implements'
		);
	}

	public function testMultipleImplementsWithSecondAndThirdWrong()
	{
		$report = $this->getFileReport();
		$this->assertSniffError(
			$report,
			23,
			FullyQualifiedClassNameAfterKeywordSniff::getErrorCode('implements'),
			'Amet in implements'
		);
		$this->assertSniffError(
			$report,
			23,
			FullyQualifiedClassNameAfterKeywordSniff::getErrorCode('implements'),
			'Omega in implements'
		);
	}

}
