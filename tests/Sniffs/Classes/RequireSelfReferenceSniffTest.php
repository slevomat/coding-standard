<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use SlevomatCodingStandard\Sniffs\TestCase;

class RequireSelfReferenceSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireSelfReferenceNoErrors.php');

		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireSelfReferenceErrors.php');

		self::assertSame(10, $report->getErrorCount());

		foreach ([14, 16, 19, 20, 23, 24, 26, 28] as $line) {
			self::assertSniffError($report, $line, RequireSelfReferenceSniff::CODE_REQUIRED_SELF_REFERENCE);
		}

		self::assertAllFixedInFile($report);
	}

	public function testAnonymousClassesErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireSelfReferenceAnonymousClassesErrors.php');

		self::assertSame(4, $report->getErrorCount());

		self::assertSniffError($report, 6, RequireSelfReferenceSniff::CODE_REQUIRED_SELF_REFERENCE);
		self::assertSniffError($report, 17, RequireSelfReferenceSniff::CODE_REQUIRED_SELF_REFERENCE);
		self::assertSniffError($report, 44, RequireSelfReferenceSniff::CODE_REQUIRED_SELF_REFERENCE);
		self::assertCount(2, $report->getErrors()[44]);

		self::assertAllFixedInFile($report);
	}

}
