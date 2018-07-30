<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Commenting;

use SlevomatCodingStandard\Sniffs\TestCase;

class UselessConstantDocCommentSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/uselessConstantDocCommentNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/uselessConstantDocCommentErrors.php');

		self::assertSame(4, $report->getErrorCount());

		self::assertSniffError($report, 5, UselessConstantDocCommentSniff::CODE_USELESS_DOC_COMMENT);
		self::assertSniffError($report, 16, UselessConstantDocCommentSniff::CODE_USELESS_VAR_ANNOTATION);
		self::assertSniffError($report, 28, UselessConstantDocCommentSniff::CODE_USELESS_VAR_ANNOTATION);
		self::assertSniffError($report, 38, UselessConstantDocCommentSniff::CODE_USELESS_DOC_COMMENT);

		self::assertAllFixedInFile($report);
	}

}
