<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Commenting;

class EmptyCommentSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/emptyCommentNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/emptyCommentErrors.php');

		self::assertSame(19, $report->getErrorCount());

		foreach ([3, 5, 7, 8, 10, 12, 15, 19, 20, 22, 24, 27, 31, 35, 39, 43, 50, 55, 60] as $line) {
			self::assertSniffError($report, $line, EmptyCommentSniff::CODE_EMPTY_COMMENT);
		}

		self::assertAllFixedInFile($report);
	}

}
