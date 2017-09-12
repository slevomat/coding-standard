<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

class FullyQualifiedExceptionsSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	private function getFileReport(): \PHP_CodeSniffer\Files\File
	{
		return $this->checkFile(__DIR__ . '/data/fullyQualifiedExceptionNames.php');
	}

	public function testNonFullyQualifiedExceptionInTypeHint(): void
	{
		$this->assertSniffError(
			$this->getFileReport(),
			3,
			FullyQualifiedExceptionsSniff::CODE_NON_FULLY_QUALIFIED_EXCEPTION,
			'FooException'
		);
	}

	public function testNonFullyQualifiedExceptionInThrow(): void
	{
		$this->assertSniffError(
			$this->getFileReport(),
			6,
			FullyQualifiedExceptionsSniff::CODE_NON_FULLY_QUALIFIED_EXCEPTION,
			'FooException'
		);
	}

	public function testNonFullyQualifiedExceptionInCatch(): void
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

	public function testFullyQualifiedExceptionInTypeHint(): void
	{
		$this->assertNoSniffError($this->getFileReport(), 16);
	}

	public function testFullyQualifiedExceptionInThrow(): void
	{
		$this->assertNoSniffError($this->getFileReport(), 19);
	}

	public function testFullyQualifiedExceptionInCatch(): void
	{
		$this->assertNoSniffError($this->getFileReport(), 20);
		$this->assertNoSniffError($this->getFileReport(), 22);
		$this->assertNoSniffError($this->getFileReport(), 24);
		$this->assertNoSniffError($this->getFileReport(), 26);
		$this->assertNoSniffError($this->getFileReport(), 28);
	}

	public function testClassSuffixedErrorOrExceptionIsNotAnExceptionButReported(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/ignoredNames.php');
		$this->assertSniffError($report, 3, FullyQualifiedExceptionsSniff::CODE_NON_FULLY_QUALIFIED_EXCEPTION);
		$this->assertSniffError($report, 7, FullyQualifiedExceptionsSniff::CODE_NON_FULLY_QUALIFIED_EXCEPTION);
	}

	public function testIgnoredNames(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/ignoredNames.php', [
			'ignoredNames' => [
				'LibXMLError',
				'LibXMLException',
			],
		]);
		$this->assertNoSniffErrorInFile($report);
	}

	public function testClassSuffixedErrorOrExceptionIsNotAnExceptionButReportedInNamespace(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/ignoredNamesInNamespace.php');
		$this->assertNoSniffError($report, 5); // *Error names are reported only with a root namespace
		$this->assertSniffError($report, 9, FullyQualifiedExceptionsSniff::CODE_NON_FULLY_QUALIFIED_EXCEPTION);
		$this->assertNoSniffError($report, 13); // look like Exception but isn't - handled by ReferenceUsedNamesOnlySniff
		$this->assertNoSniffError($report, 17); // dtto
	}

	public function testIgnoredNamesInNamespace(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/ignoredNamesInNamespace.php', [
			'ignoredNames' => [
				'IgnoredNames\\LibXMLException',
			],
		]);
		$this->assertNoSniffErrorInFile($report);
	}

	public function testFixableFullyQualified(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/fixableFullyQualifiedExceptions.php');
		$this->assertAllFixedInFile($report);
	}

}
