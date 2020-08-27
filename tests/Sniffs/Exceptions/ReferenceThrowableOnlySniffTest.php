<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Exceptions;

use SlevomatCodingStandard\Sniffs\TestCase;

class ReferenceThrowableOnlySniffTest extends TestCase
{

	public function testExceptionReferences(): void
	{
		$report = self::checkFile(__DIR__ . '/data/exceptionReferences.php');
		self::assertNoSniffError($report, 5);
		self::assertNoSniffError($report, 6);
		self::assertNoSniffError($report, 8);
		self::assertSniffError($report, 12, ReferenceThrowableOnlySniff::CODE_REFERENCED_GENERAL_EXCEPTION);
		self::assertNoSniffError($report, 13);
		self::assertNoSniffError($report, 14);
		self::assertNoSniffError($report, 17);
		self::assertNoSniffError($report, 18);
		self::assertNoSniffError($report, 23);
		self::assertNoSniffError($report, 25);
		self::assertNoSniffError($report, 27);
		self::assertSniffError($report, 33, ReferenceThrowableOnlySniff::CODE_REFERENCED_GENERAL_EXCEPTION);
		self::assertNoSniffError($report, 35);
		self::assertSniffError($report, 37, ReferenceThrowableOnlySniff::CODE_REFERENCED_GENERAL_EXCEPTION);
		self::assertNoSniffError($report, 45);
	}

	public function testExceptionReferencesWithoutNamespace(): void
	{
		$report = self::checkFile(__DIR__ . '/data/exceptionReferencesWithoutNamespace.php');
		self::assertNoSniffError($report, 3);
		self::assertNoSniffError($report, 5);
		self::assertSniffError($report, 9, ReferenceThrowableOnlySniff::CODE_REFERENCED_GENERAL_EXCEPTION);
		self::assertSniffError($report, 10, ReferenceThrowableOnlySniff::CODE_REFERENCED_GENERAL_EXCEPTION);
		self::assertNoSniffError($report, 11);
		self::assertNoSniffError($report, 14);
		self::assertNoSniffError($report, 15);
		self::assertNoSniffError($report, 20);
		self::assertNoSniffError($report, 22);
		self::assertNoSniffError($report, 24);
		self::assertSniffError($report, 30, ReferenceThrowableOnlySniff::CODE_REFERENCED_GENERAL_EXCEPTION);
		self::assertSniffError($report, 32, ReferenceThrowableOnlySniff::CODE_REFERENCED_GENERAL_EXCEPTION);
	}

	public function testExceptionReferencesUnionTypes71(): void
	{
		$report = self::checkFile(__DIR__ . '/data/exceptionReferences71.php');
		self::assertNoSniffError($report, 5);
		self::assertNoSniffError($report, 7);
		self::assertNoSniffError($report, 9);
		self::assertNoSniffError($report, 15);
		self::assertNoSniffError($report, 17);
		self::assertNoSniffError($report, 19);
		self::assertSniffError($report, 25, ReferenceThrowableOnlySniff::CODE_REFERENCED_GENERAL_EXCEPTION);
		self::assertSniffError($report, 27, ReferenceThrowableOnlySniff::CODE_REFERENCED_GENERAL_EXCEPTION);
	}

	public function testFixableExceptionReference(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/fixableExceptionReference.php',
			[],
			[ReferenceThrowableOnlySniff::CODE_REFERENCED_GENERAL_EXCEPTION]
		);
		self::assertAllFixedInFile($report);
	}

	public function testFixableExceptionReferenceWithoutNamespace(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/fixableExceptionReferenceWithoutNamespace.php',
			[],
			[ReferenceThrowableOnlySniff::CODE_REFERENCED_GENERAL_EXCEPTION]
		);
		self::assertAllFixedInFile($report);
	}

	public function testFixableExceptionReference71(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/fixableExceptionReference71.php',
			[],
			[ReferenceThrowableOnlySniff::CODE_REFERENCED_GENERAL_EXCEPTION]
		);
		self::assertAllFixedInFile($report);
	}

	public function testFixableExceptionReferenceWithoutNamespace71(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/fixableExceptionReferenceWithoutNamespace71.php',
			[],
			[ReferenceThrowableOnlySniff::CODE_REFERENCED_GENERAL_EXCEPTION]
		);
		self::assertAllFixedInFile($report);
	}

}
