<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Commenting;

use SlevomatCodingStandard\Sniffs\TestCase;

class UselessFunctionDocCommentSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/uselessFunctionDocCommentSniffNoErrors.php', [
			'traversableTypeHints' => ['Traversable'],
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/uselessFunctionDocCommentSniffErrors.php');

		self::assertSame(5, $report->getErrorCount());

		self::assertSniffError($report, 10, UselessFunctionDocCommentSniff::CODE_USELESS_DOC_COMMENT);
		self::assertSniffError($report, 18, UselessFunctionDocCommentSniff::CODE_USELESS_DOC_COMMENT);
		self::assertSniffError($report, 25, UselessFunctionDocCommentSniff::CODE_USELESS_DOC_COMMENT);
		self::assertSniffError($report, 32, UselessFunctionDocCommentSniff::CODE_USELESS_DOC_COMMENT);
		self::assertSniffError($report, 39, UselessFunctionDocCommentSniff::CODE_USELESS_DOC_COMMENT);

		self::assertAllFixedInFile($report);
	}

}
