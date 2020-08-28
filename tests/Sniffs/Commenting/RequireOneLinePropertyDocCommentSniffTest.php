<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Commenting;

use SlevomatCodingStandard\Sniffs\TestCase;

class RequireOneLinePropertyDocCommentSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/oneLinePropertyDocCommentNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/oneLinePropertyDocCommentErrors.php');

		self::assertSame(10, $report->getErrorCount());

		self::assertSniffError($report, 6, RequireOneLinePropertyDocCommentSniff::CODE_MULTI_LINE_PROPERTY_COMMENT);
		self::assertSniffError($report, 11, RequireOneLinePropertyDocCommentSniff::CODE_MULTI_LINE_PROPERTY_COMMENT);
		self::assertSniffError($report, 16, RequireOneLinePropertyDocCommentSniff::CODE_MULTI_LINE_PROPERTY_COMMENT);
		self::assertSniffError($report, 21, RequireOneLinePropertyDocCommentSniff::CODE_MULTI_LINE_PROPERTY_COMMENT);
		self::assertSniffError($report, 26, RequireOneLinePropertyDocCommentSniff::CODE_MULTI_LINE_PROPERTY_COMMENT);
		self::assertSniffError($report, 32, RequireOneLinePropertyDocCommentSniff::CODE_MULTI_LINE_PROPERTY_COMMENT);
		self::assertSniffError($report, 38, RequireOneLinePropertyDocCommentSniff::CODE_MULTI_LINE_PROPERTY_COMMENT);
		self::assertSniffError($report, 46, RequireOneLinePropertyDocCommentSniff::CODE_MULTI_LINE_PROPERTY_COMMENT);
		self::assertSniffError($report, 50, RequireOneLinePropertyDocCommentSniff::CODE_MULTI_LINE_PROPERTY_COMMENT);
		self::assertSniffError($report, 54, RequireOneLinePropertyDocCommentSniff::CODE_MULTI_LINE_PROPERTY_COMMENT);

		self::assertAllFixedInFile($report);
	}

}
