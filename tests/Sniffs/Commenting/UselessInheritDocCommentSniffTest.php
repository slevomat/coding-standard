<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Commenting;

use SlevomatCodingStandard\Sniffs\TestCase;

class UselessInheritDocCommentSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/uselessInheritDocCommentSniffNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/uselessInheritDocCommentSniffErrors.php');

		self::assertSame(6, $report->getErrorCount());

		self::assertSniffError($report, 3, UselessInheritDocCommentSniff::CODE_USELESS_INHERIT_DOC_COMMENT);
		self::assertSniffError($report, 11, UselessInheritDocCommentSniff::CODE_USELESS_INHERIT_DOC_COMMENT);
		self::assertSniffError($report, 19, UselessInheritDocCommentSniff::CODE_USELESS_INHERIT_DOC_COMMENT);
		self::assertSniffError($report, 27, UselessInheritDocCommentSniff::CODE_USELESS_INHERIT_DOC_COMMENT);
		self::assertSniffError($report, 41, UselessInheritDocCommentSniff::CODE_USELESS_INHERIT_DOC_COMMENT);
		self::assertSniffError($report, 49, UselessInheritDocCommentSniff::CODE_USELESS_INHERIT_DOC_COMMENT);

		self::assertAllFixedInFile($report);
	}

}
