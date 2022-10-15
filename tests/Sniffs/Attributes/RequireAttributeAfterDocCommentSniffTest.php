<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Attributes;

use SlevomatCodingStandard\Sniffs\TestCase;

class RequireAttributeAfterDocCommentSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireAttributeAfterDocCommentNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireAttributeAfterDocCommentErrors.php');

		self::assertSame(3, $report->getErrorCount());

		self::assertSniffError($report, 4, RequireAttributeAfterDocCommentSniff::CODE_ATTRIBUTE_BEFORE_DOC_COMMENT);
		self::assertSniffError($report, 10, RequireAttributeAfterDocCommentSniff::CODE_ATTRIBUTE_BEFORE_DOC_COMMENT);
		self::assertSniffError($report, 17, RequireAttributeAfterDocCommentSniff::CODE_ATTRIBUTE_BEFORE_DOC_COMMENT);

		self::assertAllFixedInFile($report);
	}

	public function testInvalidAttributePriorPhp80(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireAttributeAfterDocCommentInvalidAttribute.php');
		self::assertNoSniffErrorInFile($report);
	}

}
