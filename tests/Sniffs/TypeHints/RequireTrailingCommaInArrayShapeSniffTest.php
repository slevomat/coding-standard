<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use SlevomatCodingStandard\Sniffs\TestCase;

class RequireTrailingCommaInArrayShapeSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireTrailingCommaInArrayShapeNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireTrailingCommaInArrayShapeErrors.php');

		self::assertSame(12, $report->getErrorCount());

		foreach ([6, 9, 17, 24, 31, 34, 36, 45, 53, 61, 62] as $line) {
			self::assertSniffError($report, $line, RequireTrailingCommaInArrayShapeSniff::CODE_MISSING_TRAILING_COMMA);
		}

		self::assertAllFixedInFile($report);
	}

}
