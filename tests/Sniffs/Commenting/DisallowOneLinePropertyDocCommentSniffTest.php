<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Commenting;

use SlevomatCodingStandard\Sniffs\TestCase;

class DisallowOneLinePropertyDocCommentSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/multiLinePropertyDocCommentNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/multiLinePropertyDocCommentErrors.php');

		self::assertSame(12, $report->getErrorCount());

		self::assertSniffError($report, 6, DisallowOneLinePropertyDocCommentSniff::CODE_ONE_LINE_PROPERTY_COMMENT);
		self::assertSniffError($report, 9, DisallowOneLinePropertyDocCommentSniff::CODE_ONE_LINE_PROPERTY_COMMENT);
		self::assertSniffError($report, 12, DisallowOneLinePropertyDocCommentSniff::CODE_ONE_LINE_PROPERTY_COMMENT);
		self::assertSniffError($report, 15, DisallowOneLinePropertyDocCommentSniff::CODE_ONE_LINE_PROPERTY_COMMENT);
		self::assertSniffError($report, 18, DisallowOneLinePropertyDocCommentSniff::CODE_ONE_LINE_PROPERTY_COMMENT);
		self::assertSniffError($report, 21, DisallowOneLinePropertyDocCommentSniff::CODE_ONE_LINE_PROPERTY_COMMENT);
		self::assertSniffError($report, 24, DisallowOneLinePropertyDocCommentSniff::CODE_ONE_LINE_PROPERTY_COMMENT);
		self::assertSniffError($report, 27, DisallowOneLinePropertyDocCommentSniff::CODE_ONE_LINE_PROPERTY_COMMENT);
		self::assertSniffError($report, 30, DisallowOneLinePropertyDocCommentSniff::CODE_ONE_LINE_PROPERTY_COMMENT);
		self::assertSniffError($report, 33, DisallowOneLinePropertyDocCommentSniff::CODE_ONE_LINE_PROPERTY_COMMENT);
		self::assertSniffError($report, 36, DisallowOneLinePropertyDocCommentSniff::CODE_ONE_LINE_PROPERTY_COMMENT);
		self::assertSniffError($report, 39, DisallowOneLinePropertyDocCommentSniff::CODE_ONE_LINE_PROPERTY_COMMENT);

		self::assertAllFixedInFile($report);
	}

}
