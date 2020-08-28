<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Commenting;

use SlevomatCodingStandard\Sniffs\TestCase;

class RequireOneLineDocCommentSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/oneLineDocCommentSniffNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/oneLineDocCommentSniffErrors.php');

		self::assertSame(15, $report->getErrorCount());

		self::assertSniffError($report, 6, RequireOneLineDocCommentSniff::CODE_MULTI_LINE_DOC_COMMENT);
		self::assertSniffError($report, 11, RequireOneLineDocCommentSniff::CODE_MULTI_LINE_DOC_COMMENT);
		self::assertSniffError($report, 16, RequireOneLineDocCommentSniff::CODE_MULTI_LINE_DOC_COMMENT);
		self::assertSniffError($report, 21, RequireOneLineDocCommentSniff::CODE_MULTI_LINE_DOC_COMMENT);
		self::assertSniffError($report, 26, RequireOneLineDocCommentSniff::CODE_MULTI_LINE_DOC_COMMENT);
		self::assertSniffError($report, 32, RequireOneLineDocCommentSniff::CODE_MULTI_LINE_DOC_COMMENT);
		self::assertSniffError($report, 38, RequireOneLineDocCommentSniff::CODE_MULTI_LINE_DOC_COMMENT);
		self::assertSniffError($report, 46, RequireOneLineDocCommentSniff::CODE_MULTI_LINE_DOC_COMMENT);
		self::assertSniffError($report, 50, RequireOneLineDocCommentSniff::CODE_MULTI_LINE_DOC_COMMENT);
		self::assertSniffError($report, 54, RequireOneLineDocCommentSniff::CODE_MULTI_LINE_DOC_COMMENT);

		self::assertSniffError($report, 61, RequireOneLineDocCommentSniff::CODE_MULTI_LINE_DOC_COMMENT);
		self::assertSniffError($report, 66, RequireOneLineDocCommentSniff::CODE_MULTI_LINE_DOC_COMMENT);
		self::assertSniffError($report, 71, RequireOneLineDocCommentSniff::CODE_MULTI_LINE_DOC_COMMENT);
		self::assertSniffError($report, 76, RequireOneLineDocCommentSniff::CODE_MULTI_LINE_DOC_COMMENT);
		self::assertSniffError($report, 81, RequireOneLineDocCommentSniff::CODE_MULTI_LINE_DOC_COMMENT);

		self::assertAllFixedInFile($report);
	}

}
