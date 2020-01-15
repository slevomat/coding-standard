<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use SlevomatCodingStandard\Sniffs\TestCase;

final class RequireSingleLineMethodDeclarationSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$file = self::checkFile(__DIR__ . '/data/requireSingleLineMethodDeclarationNoErrors.php');
		self::assertNoSniffErrorInFile($file);
	}

	public function testErrors(): void
	{
		$file = self::checkFile(__DIR__ . '/data/requireSingleLineMethodDeclarationErrors.php');
		self::assertSame(4, $file->getErrorCount());

		self::assertSniffError($file, 5, RequireSingleLineMethodDeclarationSniff::CODE_UNNECESSARY_MULTI_LINE_METHOD);
		self::assertSniffError($file, 11, RequireSingleLineMethodDeclarationSniff::CODE_UNNECESSARY_MULTI_LINE_METHOD);
		self::assertSniffError($file, 20, RequireSingleLineMethodDeclarationSniff::CODE_UNNECESSARY_MULTI_LINE_METHOD);
		self::assertSniffError($file, 25, RequireSingleLineMethodDeclarationSniff::CODE_UNNECESSARY_MULTI_LINE_METHOD);

		self::assertAllFixedInFile($file);
	}

	public function testForAllMethods(): void
	{
		$file = self::checkFile(__DIR__ . '/data/requireSingleLineMethodDeclarationAllMethodsErrors.php', ['maxLineLength' => 0]);
		self::assertSame(1, $file->getErrorCount());

		self::assertSniffError($file, 5, RequireSingleLineMethodDeclarationSniff::CODE_UNNECESSARY_MULTI_LINE_METHOD);

		self::assertAllFixedInFile($file);
	}

}
