<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Commenting;

use SlevomatCodingStandard\Sniffs\TestCase;

class RequireOneLineDocCommentSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		self::assertNoSniffErrorInFile(self::checkFile(__DIR__ . '/data/oneLineDocCommentSniffNoErrors.php'));
	}

	public function testErrors(): void
	{
		$file = self::checkFile(__DIR__ . '/data/oneLineDocCommentSniffErrors.php');

		self::assertSame(15, $file->getErrorCount());

		self::assertSniffError($file, 6, RequireOneLineDocCommentSniff::CODE_MULTI_LINE_DOC_COMMENT);
		self::assertSniffError($file, 11, RequireOneLineDocCommentSniff::CODE_MULTI_LINE_DOC_COMMENT);
		self::assertSniffError($file, 16, RequireOneLineDocCommentSniff::CODE_MULTI_LINE_DOC_COMMENT);
		self::assertSniffError($file, 21, RequireOneLineDocCommentSniff::CODE_MULTI_LINE_DOC_COMMENT);
		self::assertSniffError($file, 26, RequireOneLineDocCommentSniff::CODE_MULTI_LINE_DOC_COMMENT);
		self::assertSniffError($file, 32, RequireOneLineDocCommentSniff::CODE_MULTI_LINE_DOC_COMMENT);
		self::assertSniffError($file, 38, RequireOneLineDocCommentSniff::CODE_MULTI_LINE_DOC_COMMENT);
		self::assertSniffError($file, 46, RequireOneLineDocCommentSniff::CODE_MULTI_LINE_DOC_COMMENT);
		self::assertSniffError($file, 50, RequireOneLineDocCommentSniff::CODE_MULTI_LINE_DOC_COMMENT);
		self::assertSniffError($file, 54, RequireOneLineDocCommentSniff::CODE_MULTI_LINE_DOC_COMMENT);

		self::assertSniffError($file, 61, RequireOneLineDocCommentSniff::CODE_MULTI_LINE_DOC_COMMENT);
		self::assertSniffError($file, 66, RequireOneLineDocCommentSniff::CODE_MULTI_LINE_DOC_COMMENT);
		self::assertSniffError($file, 71, RequireOneLineDocCommentSniff::CODE_MULTI_LINE_DOC_COMMENT);
		self::assertSniffError($file, 76, RequireOneLineDocCommentSniff::CODE_MULTI_LINE_DOC_COMMENT);
		self::assertSniffError($file, 81, RequireOneLineDocCommentSniff::CODE_MULTI_LINE_DOC_COMMENT);

		self::assertAllFixedInFile($file);
	}

}
