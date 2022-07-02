<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Files;

use SlevomatCodingStandard\Sniffs\TestCase;

class FileLengthSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/fileLength.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/fileLength.php', [
			'maxLinesLength' => 5,
			'includeComments' => true,
		]);

		self::assertSame(1, $report->getErrorCount());

		self::assertSniffError(
			$report,
			1,
			FileLengthSniff::CODE_FILE_TOO_LONG,
			'Your file is too long. Currently using 12 lines. Can be up to 5 lines.'
		);
	}

}
