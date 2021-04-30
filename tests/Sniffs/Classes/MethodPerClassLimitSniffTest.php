<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use SlevomatCodingStandard\Sniffs\TestCase;

class MethodPerClassLimitSniffTest extends TestCase
{

	public function testClassNoErrors(): void
	{
		$phpcsFile = self::checkFile(__DIR__ . '/data/methodPerClassLimitNoErrors.php');
		self::assertNoSniffErrorInFile($phpcsFile);
	}

	public function testClassErrors(): void
	{
		$phpcsFile = self::checkFile(__DIR__ . '/data/methodPerClassLimitErrors.php');
		self::assertSame(1, $phpcsFile->getErrorCount());
		self::assertSniffError($phpcsFile, 3, MethodPerClassLimitSniff::CODE_METHOD_PER_CLASS_LIMIT);
	}

	public function testAnonymousClassNoErrors(): void
	{
		$phpcsFile = self::checkFile(__DIR__ . '/data/methodPerAnonymousClassLimitNoErrors.php');
		self::assertNoSniffErrorInFile($phpcsFile);
	}

	public function testAnonymousClassErrors(): void
	{
		$phpcsFile = self::checkFile(__DIR__ . '/data/methodPerAnonymousClassLimitErrors.php');
		self::assertSame(1, $phpcsFile->getErrorCount());
		self::assertSniffError($phpcsFile, 3, MethodPerClassLimitSniff::CODE_METHOD_PER_CLASS_LIMIT);
	}

	public function testTraitNoErrors(): void
	{
		$phpcsFile = self::checkFile(__DIR__ . '/data/methodPerTraitLimitNoErrors.php');
		self::assertNoSniffErrorInFile($phpcsFile);
	}

	public function testTraitErrors(): void
	{
		$phpcsFile = self::checkFile(__DIR__ . '/data/methodPerTraitLimitErrors.php');
		self::assertSame(1, $phpcsFile->getErrorCount());
		self::assertSniffError($phpcsFile, 3, MethodPerClassLimitSniff::CODE_METHOD_PER_CLASS_LIMIT);
	}

	public function testInterfaceNoErrors(): void
	{
		$phpcsFile = self::checkFile(__DIR__ . '/data/methodPerInterfaceLimitNoErrors.php');
		self::assertNoSniffErrorInFile($phpcsFile);
	}

	public function testInterfaceErrors(): void
	{
		$phpcsFile = self::checkFile(__DIR__ . '/data/methodPerInterfaceLimitErrors.php');
		self::assertSame(1, $phpcsFile->getErrorCount());
		self::assertSniffError($phpcsFile, 3, MethodPerClassLimitSniff::CODE_METHOD_PER_CLASS_LIMIT);
	}

}
