<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Commenting;

use SlevomatCodingStandard\Sniffs\TestCase;

class ThrowsAnnotationsOrderSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/throwsAnnotationsOrderNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/throwsAnnotationsOrderErrors.php');

		self::assertSame(7, $report->getErrorCount());

		self::assertSniffError($report, 10, ThrowsAnnotationsOrderSniff::CODE_INCORRECT_ORDER);
		self::assertSniffError($report, 20, ThrowsAnnotationsOrderSniff::CODE_INCORRECT_ORDER);
		self::assertSniffError($report, 31, ThrowsAnnotationsOrderSniff::CODE_INCORRECT_ORDER);
		self::assertSniffError($report, 40, ThrowsAnnotationsOrderSniff::CODE_INCORRECT_ORDER);
		self::assertSniffError($report, 48, ThrowsAnnotationsOrderSniff::CODE_INCORRECT_ORDER);
		self::assertSniffError($report, 59, ThrowsAnnotationsOrderSniff::CODE_INCORRECT_ORDER);
		self::assertSniffError($report, 67, ThrowsAnnotationsOrderSniff::CODE_INCORRECT_ORDER);

		self::assertAllFixedInFile($report);
	}

}
