<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use SlevomatCodingStandard\Sniffs\TestCase;

class ClassLengthSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/classLength.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/classLength.php', [
			'maxLinesLength' => 5,
			'includeComments' => true,
		]);

		self::assertSame(1, $report->getErrorCount());

		self::assertSniffError(
			$report,
			5,
			ClassLengthSniff::CODE_CLASS_TOO_LONG,
			'Your class is too long. Currently using 13 lines. Can be up to 5 lines.'
		);
	}

}
