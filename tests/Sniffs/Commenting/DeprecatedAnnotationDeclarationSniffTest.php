<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Commenting;

use SlevomatCodingStandard\Sniffs\TestCase;

class DeprecatedAnnotationDeclarationSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/deprecatedAnnotationDeclarationNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/deprecatedAnnotationDeclarationErrors.php');

		self::assertSame(4, $report->getErrorCount());

		foreach ([6, 12, 17, 25] as $line) {
			self::assertSniffError($report, $line, DeprecatedAnnotationDeclarationSniff::MISSING_DESCRIPTION);
		}
	}

}
