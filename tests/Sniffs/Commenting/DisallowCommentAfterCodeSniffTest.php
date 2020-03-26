<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Commenting;

use SlevomatCodingStandard\Sniffs\TestCase;

class DisallowCommentAfterCodeSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowCommentAfterCodeNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowCommentAfterCodeErrors.php');

		self::assertSame(19, $report->getErrorCount());

		foreach ([7, 9, 11, 13, 15, 19, 25, 27, 30, 35, 36, 39, 40, 41, 42, 43, 44, 45, 48] as $line) {
			self::assertSniffError($report, $line, DisallowCommentAfterCodeSniff::CODE_DISALLOWED_COMMENT_AFTER_CODE);
		}

		self::assertAllFixedInFile($report);
	}

}
