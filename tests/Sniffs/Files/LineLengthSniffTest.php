<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Files;

use SlevomatCodingStandard\Sniffs\TestCase;

class LineLengthSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		self::assertNoSniffErrorInFile(self::checkFile(__DIR__ . '/data/LineLengthSniffNoErrors.php'));
	}

	public function testErrors(): void
	{
		$file = self::checkFile(__DIR__ . '/data/LineLengthSniffErrors.php', ['ignoreImports' => false]);

		self::assertSame(7, $file->getErrorCount());

		self::assertSniffError($file, 5, LineLengthSniff::CODE_LINE_TOO_LONG);
		self::assertSniffError($file, 7, LineLengthSniff::CODE_LINE_TOO_LONG);
		self::assertSniffError($file, 10, LineLengthSniff::CODE_LINE_TOO_LONG);
		self::assertSniffError($file, 12, LineLengthSniff::CODE_LINE_TOO_LONG);
		self::assertSniffError($file, 15, LineLengthSniff::CODE_LINE_TOO_LONG);
		self::assertSniffError($file, 19, LineLengthSniff::CODE_LINE_TOO_LONG);
		self::assertSniffError($file, 20, LineLengthSniff::CODE_LINE_TOO_LONG);
	}

	public function testErrorsIgnoreComments(): void
	{
		$file = self::checkFile(__DIR__ . '/data/LineLengthSniffErrorsIgnoreComments.php', ['ignoreComments' => true]);

		self::assertSame(4, $file->getErrorCount());

		self::assertSniffError($file, 7, LineLengthSniff::CODE_LINE_TOO_LONG);
		self::assertSniffError($file, 12, LineLengthSniff::CODE_LINE_TOO_LONG);
		self::assertSniffError($file, 15, LineLengthSniff::CODE_LINE_TOO_LONG);
		self::assertSniffError($file, 20, LineLengthSniff::CODE_LINE_TOO_LONG);
	}

	public function testErrorsIgnoreImports(): void
	{
		$file = self::checkFile(__DIR__ . '/data/LineLengthSniffErrorsIgnoreImports.php');

		self::assertSame(7, $file->getErrorCount());

		self::assertSniffError($file, 7, LineLengthSniff::CODE_LINE_TOO_LONG);
		self::assertSniffError($file, 9, LineLengthSniff::CODE_LINE_TOO_LONG);
		self::assertSniffError($file, 12, LineLengthSniff::CODE_LINE_TOO_LONG);
		self::assertSniffError($file, 14, LineLengthSniff::CODE_LINE_TOO_LONG);
		self::assertSniffError($file, 17, LineLengthSniff::CODE_LINE_TOO_LONG);
		self::assertSniffError($file, 21, LineLengthSniff::CODE_LINE_TOO_LONG);
		self::assertSniffError($file, 22, LineLengthSniff::CODE_LINE_TOO_LONG);
	}

}
