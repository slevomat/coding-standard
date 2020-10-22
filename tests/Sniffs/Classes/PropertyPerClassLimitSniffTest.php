<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use SlevomatCodingStandard\Sniffs\TestCase;

class PropertyPerClassLimitSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/propertyPerClassLimitNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/propertyPerClassLimitErrors.php');

		self::assertSame(2, $report->getErrorCount());

		self::assertSniffError($report, 3, PropertyPerClassLimitSniff::CODE_PROPERTY_PER_CLASS_LIMIT);
	}

}
