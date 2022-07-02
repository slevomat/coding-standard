<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use SlevomatCodingStandard\Sniffs\TestCase;

class ClassLineCountSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/classLineCount.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/classLineCount.php', [
			'maxLinesLength' => 5,
			'includeComments' => true,
		]);

		self::assertSame(1, $report->getErrorCount());

		self::assertSniffError(
			$report,
			5,
			ClassLineCountSniff::CODE_CLASS_LINE_COUNT,
			'Your class is too long. Currently using 13 lines. Can be up to 5 lines.'
		);
	}

}
