<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use SlevomatCodingStandard\Sniffs\TestCase;

class DisallowMultiPropertyDefinitionSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowMultiPropertyDefinitionNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowMultiPropertyDefinitionErrors.php');

		self::assertSame(6, $report->getErrorCount());

		self::assertSniffError($report, 6, DisallowMultiPropertyDefinitionSniff::CODE_DISALLOWED_MULTI_PROPERTY_DEFINITION);
		self::assertSniffError($report, 8, DisallowMultiPropertyDefinitionSniff::CODE_DISALLOWED_MULTI_PROPERTY_DEFINITION);
		self::assertSniffError($report, 11, DisallowMultiPropertyDefinitionSniff::CODE_DISALLOWED_MULTI_PROPERTY_DEFINITION);
		self::assertSniffError($report, 13, DisallowMultiPropertyDefinitionSniff::CODE_DISALLOWED_MULTI_PROPERTY_DEFINITION);
		self::assertSniffError($report, 24, DisallowMultiPropertyDefinitionSniff::CODE_DISALLOWED_MULTI_PROPERTY_DEFINITION);
		self::assertSniffError($report, 26, DisallowMultiPropertyDefinitionSniff::CODE_DISALLOWED_MULTI_PROPERTY_DEFINITION);

		self::assertAllFixedInFile($report);
	}

}
