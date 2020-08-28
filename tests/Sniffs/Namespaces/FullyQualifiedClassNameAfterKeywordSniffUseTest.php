<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use PHP_CodeSniffer\Files\File;
use SlevomatCodingStandard\Sniffs\TestCase;

class FullyQualifiedClassNameAfterKeywordSniffUseTest extends TestCase
{

	public function testIgnoreUseInClosure(): void
	{
		self::assertNoSniffError($this->getFileReport(), 34);
	}

	public function testIgnoreUseInNamespace(): void
	{
		self::assertNoSniffError($this->getFileReport(), 5);
	}

	public function testIgnoreUseInNamespaceWithParenthesis(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/fullyQualifiedUseNamespaceWithParenthesis.php',
			['keywordsToCheck' => ['T_USE']]
		);
		self::assertNoSniffErrorInFile($report);
	}

	public function testNonFullyQualifiedUse(): void
	{
		self::assertSniffError(
			$this->getFileReport(),
			14,
			FullyQualifiedClassNameAfterKeywordSniff::getErrorCode('use'),
			'Dolor in use'
		);
	}

	public function testFullyQualifiedUse(): void
	{
		self::assertNoSniffError($this->getFileReport(), 9);
	}

	public function testMultipleFullyQualifiedUse(): void
	{
		self::assertNoSniffError($this->getFileReport(), 19);
	}

	public function testMultipleUseWithFirstWrong(): void
	{
		self::assertSniffError(
			$this->getFileReport(),
			24,
			FullyQualifiedClassNameAfterKeywordSniff::getErrorCode('use'),
			'Dolor in use'
		);
	}

	public function testMultipleUseWithSecondAndThirdWrong(): void
	{
		$report = $this->getFileReport();
		self::assertSniffError(
			$report,
			29,
			FullyQualifiedClassNameAfterKeywordSniff::getErrorCode('use'),
			'Amet in use'
		);
		self::assertSniffError(
			$report,
			29,
			FullyQualifiedClassNameAfterKeywordSniff::getErrorCode('use'),
			'Omega in use'
		);
	}

	protected static function getSniffClassName(): string
	{
		return FullyQualifiedClassNameAfterKeywordSniff::class;
	}

	private function getFileReport(): File
	{
		return self::checkFile(
			__DIR__ . '/data/fullyQualifiedUse.php',
			['keywordsToCheck' => ['T_USE']]
		);
	}

}
