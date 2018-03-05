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

		self::assertSame(33, $report->getErrorCount());

		foreach ([3, 5, 7, 8, 10, 12, 15, 19, 20, 22, 24, 27, 31, 35, 39, 43, 50, 55, 60, 65, 69, 74, 76, 84, 86, 91, 96, 97, 98, 103, 104, 107, 113] as $line) {
			self::assertSniffError($report, $line, EmptyCommentSniff::CODE_EMPTY_COMMENT);
		}

		self::assertAllFixedInFile($report);
	}

}
