<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use PHP_CodeSniffer\Files\File;
use SlevomatCodingStandard\Sniffs\TestCase;
use Throwable;
use TypeError;

class FullyQualifiedExceptionsSniffTest extends TestCase
{

	public function testNonFullyQualifiedExceptionInTypeHint(): void
	{
		self::assertSniffError(
			$this->getFileReport(),
			3,
			FullyQualifiedExceptionsSniff::CODE_NON_FULLY_QUALIFIED_EXCEPTION,
			'FooException'
		);
	}

	public function testNonFullyQualifiedExceptionInThrow(): void
	{
		self::assertSniffError(
			$this->getFileReport(),
			6,
			FullyQualifiedExceptionsSniff::CODE_NON_FULLY_QUALIFIED_EXCEPTION,
			'FooException'
		);
	}

	public function testNonFullyQualifiedExceptionInCatch(): void
	{
		self::assertSniffError(
			$this->getFileReport(),
			7,
			FullyQualifiedExceptionsSniff::CODE_NON_FULLY_QUALIFIED_EXCEPTION,
			'BarException'
		);
		self::assertSniffError(
			$this->getFileReport(),
			9,
			FullyQualifiedExceptionsSniff::CODE_NON_FULLY_QUALIFIED_EXCEPTION,
			Throwable::class
		);
		self::assertSniffError(
			$this->getFileReport(),
			11,
			FullyQualifiedExceptionsSniff::CODE_NON_FULLY_QUALIFIED_EXCEPTION,
			TypeError::class
		);
	}

	public function testFullyQualifiedExceptionInTypeHint(): void
	{
		self::assertNoSniffError($this->getFileReport(), 16);
	}

	public function testFullyQualifiedExceptionInThrow(): void
	{
		self::assertNoSniffError($this->getFileReport(), 19);
	}

	public function testFullyQualifiedExceptionInCatch(): void
	{
		self::assertNoSniffError($this->getFileReport(), 20);
		self::assertNoSniffError($this->getFileReport(), 22);
		self::assertNoSniffError($this->getFileReport(), 24);
		self::assertNoSniffError($this->getFileReport(), 26);
		self::assertNoSniffError($this->getFileReport(), 28);
	}

	public function testClassSuffixedErrorOrExceptionIsNotAnExceptionButReported(): void
	{
		$report = self::checkFile(__DIR__ . '/data/ignoredNames.php');
		self::assertSniffError($report, 3, FullyQualifiedExceptionsSniff::CODE_NON_FULLY_QUALIFIED_EXCEPTION);
		self::assertSniffError($report, 7, FullyQualifiedExceptionsSniff::CODE_NON_FULLY_QUALIFIED_EXCEPTION);
	}

	public function testIgnoredNames(): void
	{
		$report = self::checkFile(__DIR__ . '/data/ignoredNames.php', [
			'ignoredNames' => [
				'LibXMLError',
				'LibXMLException',
			],
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testClassSuffixedErrorOrExceptionIsNotAnExceptionButReportedInNamespace(): void
	{
		$report = self::checkFile(__DIR__ . '/data/ignoredNamesInNamespace.php');
		// *Error names are reported only with a root namespace
		self::assertNoSniffError($report, 5);
		self::assertSniffError($report, 9, FullyQualifiedExceptionsSniff::CODE_NON_FULLY_QUALIFIED_EXCEPTION);
		// look like Exception but isn't - handled by ReferenceUsedNamesOnlySniff
		self::assertNoSniffError($report, 13);
		// dtto
		self::assertNoSniffError($report, 17);
	}

	public function testIgnoredNamesInNamespace(): void
	{
		$report = self::checkFile(__DIR__ . '/data/ignoredNamesInNamespace.php', [
			'ignoredNames' => [
				'IgnoredNames\\LibXMLException',
			],
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testFixableFullyQualified(): void
	{
		$report = self::checkFile(__DIR__ . '/data/fixableFullyQualifiedExceptions.php');
		self::assertAllFixedInFile($report);
	}

	private function getFileReport(): File
	{
		return self::checkFile(__DIR__ . '/data/fullyQualifiedExceptionNames.php');
	}

}
