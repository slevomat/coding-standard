<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Commenting;

use SlevomatCodingStandard\Sniffs\TestCase;

class RequireOneDocCommentSniffTest extends TestCase
{

	public function testInvalidInlineDocCommentDeclarations(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireOneDocCommentSniff.php');

		self::assertSame(4, $report->getErrorCount());

		self::assertSniffError($report, 6, RequireOneDocCommentSniff::CODE_MULTIPLE_DOC_COMMENTS);
		self::assertSniffError($report, 15, RequireOneDocCommentSniff::CODE_MULTIPLE_DOC_COMMENTS);
		self::assertSniffError($report, 24, RequireOneDocCommentSniff::CODE_MULTIPLE_DOC_COMMENTS);
		self::assertSniffError($report, 49, RequireOneDocCommentSniff::CODE_MULTIPLE_DOC_COMMENTS);
	}

	public function testNoErrorsWithDocCommentAboveReturnAllowed(): void
	{
		$report = self::checkFile(__DIR__ . '/data/oneLinePropertyDocCommentErrors.fixed.php', [
			'allowDocCommentAboveReturn' => true,
		]);
		self::assertNoSniffErrorInFile($report);
	}

}
