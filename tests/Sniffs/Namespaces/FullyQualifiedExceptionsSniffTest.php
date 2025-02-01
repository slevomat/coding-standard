<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use SlevomatCodingStandard\Sniffs\TestCase;
use Throwable;
use TypeError;

class FullyQualifiedExceptionsSniffTest extends TestCase
{

	public function test(): void
	{
		$report = self::checkFile(__DIR__ . '/data/fullyQualifiedExceptionNames.php');

		// Not fully qualified exception in type hint
		self::assertSniffError($report, 3, FullyQualifiedExceptionsSniff::CODE_NON_FULLY_QUALIFIED_EXCEPTION, 'FooException');

		// Not fully qualified exception in throw
		self::assertSniffError($report, 6, FullyQualifiedExceptionsSniff::CODE_NON_FULLY_QUALIFIED_EXCEPTION, 'FooException');

		// Not fully qualified exception in catch
		self::assertSniffError($report, 7, FullyQualifiedExceptionsSniff::CODE_NON_FULLY_QUALIFIED_EXCEPTION, 'BarException');
		self::assertSniffError($report, 9, FullyQualifiedExceptionsSniff::CODE_NON_FULLY_QUALIFIED_EXCEPTION, Throwable::class);
		self::assertSniffError($report, 11, FullyQualifiedExceptionsSniff::CODE_NON_FULLY_QUALIFIED_EXCEPTION, TypeError::class);

		// Fully qualified exception in type hint
		self::assertNoSniffError($report, 16);

		// Fully qualified exception in throw
		self::assertNoSniffError($report, 19);

		// Fully qualified exception in catch
		self::assertNoSniffError($report, 20);
		self::assertNoSniffError($report, 22);
		self::assertNoSniffError($report, 24);
		self::assertNoSniffError($report, 26);
		self::assertNoSniffError($report, 28);
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

	public function testWithSpecialExceptionNames(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/fullyQualifiedSpecialExceptionNames.php',
			[
				'specialExceptionNames' => [
					'Foo\SomeError',
					'Lorem\Ipsum\SomeOtherError',
				],
			],
		);

		// Throwing used exception
		self::assertSniffError($report, 12, FullyQualifiedExceptionsSniff::CODE_NON_FULLY_QUALIFIED_EXCEPTION, 'FooError');

		// Catching partially used exception
		self::assertSniffError($report, 13, FullyQualifiedExceptionsSniff::CODE_NON_FULLY_QUALIFIED_EXCEPTION, 'Foo\SomeException');

		// Throwing exception from same namespace
		self::assertSniffError($report, 18, FullyQualifiedExceptionsSniff::CODE_NON_FULLY_QUALIFIED_EXCEPTION, 'SomeOtherError');

		// Catching fully qualified exception
		self::assertNoSniffError($report, 15);

		// Catching fully qualified exception from same namespace
		self::assertNoSniffError($report, 17);
	}

}
