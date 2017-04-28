<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

class FullyQualifiedExceptionsSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	private function getFileReport(): \PHP_CodeSniffer\Files\File
	{
		return $this->checkFile(__DIR__ . '/data/fullyQualifiedExceptionNames.php');
	}

	public function testNonFullyQualifiedExceptionInTypeHint()
	{
		$this->assertSniffError(
			$this->getFileReport(),
			3,
			FullyQualifiedExceptionsSniff::CODE_NON_FULLY_QUALIFIED_EXCEPTION,
			'FooException'
		);
	}

	public function testNonFullyQualifiedExceptionInThrow()
	{
		$this->assertSniffError(
			$this->getFileReport(),
			6,
			FullyQualifiedExceptionsSniff::CODE_NON_FULLY_QUALIFIED_EXCEPTION,
			'FooException'
		);
	}

	public function testNonFullyQualifiedExceptionInCatch()
	{
		$this->assertSniffError(
			$this->getFileReport(),
			7,
			FullyQualifiedExceptionsSniff::CODE_NON_FULLY_QUALIFIED_EXCEPTION,
			'BarException'
		);
		$this->assertSniffError(
			$this->getFileReport(),
			9,
			FullyQualifiedExceptionsSniff::CODE_NON_FULLY_QUALIFIED_EXCEPTION,
			\Throwable::class
		);
		$this->assertSniffError(
			$this->getFileReport(),
			11,
			FullyQualifiedExceptionsSniff::CODE_NON_FULLY_QUALIFIED_EXCEPTION,
			\TypeError::class
		);
	}

	public function testFullyQualifiedExceptionInTypeHint()
	{
		$this->assertNoSniffError($this->getFileReport(), 16);
	}

	public function testFullyQualifiedExceptionInThrow()
	{
		$this->assertNoSniffError($this->getFileReport(), 19);
	}

	public function testFullyQualifiedExceptionInCatch()
	{
		$this->assertNoSniffError($this->getFileReport(), 20);
		$this->assertNoSniffError($this->getFileReport(), 22);
		$this->assertNoSniffError($this->getFileReport(), 24);
		$this->assertNoSniffError($this->getFileReport(), 26);
		$this->assertNoSniffError($this->getFileReport(), 28);
	}

	public function testClassSuffixedErrorOrExceptionIsNotAnExceptionButReported()
	{
		$report = $this->checkFile(__DIR__ . '/data/ignoredNames.php');
		$this->assertSniffError($report, 3, FullyQualifiedExceptionsSniff::CODE_NON_FULLY_QUALIFIED_EXCEPTION);
		$this->assertSniffError($report, 7, FullyQualifiedExceptionsSniff::CODE_NON_FULLY_QUALIFIED_EXCEPTION);
	}

	public function testIgnoredNames()
	{
		$report = $this->checkFile(__DIR__ . '/data/ignoredNames.php', [
			'ignoredNames' => [
				'LibXMLError',
				'LibXMLException',
			],
		]);
		$this->assertNoSniffErrorInFile($report);
	}

	public function testClassSuffixedErrorOrExceptionIsNotAnExceptionButReportedInNamespace()
	{
		$report = $this->checkFile(__DIR__ . '/data/ignoredNamesInNamespace.php');
		$this->assertNoSniffError($report, 5); // *Error names are reported only with a root namespace
		$this->assertSniffError($report, 9, FullyQualifiedExceptionsSniff::CODE_NON_FULLY_QUALIFIED_EXCEPTION);
		$this->assertNoSniffError($report, 13); // look like Exception but isn't - handled by ReferenceUsedNamesOnlySniff
		$this->assertNoSniffError($report, 17); // dtto
	}

	public function testIgnoredNamesInNamespace()
	{
		$report = $this->checkFile(__DIR__ . '/data/ignoredNamesInNamespace.php', [
			'ignoredNames' => [
				'IgnoredNames\\LibXMLException',
			],
		]);
		$this->assertNoSniffErrorInFile($report);
	}

}
