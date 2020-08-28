<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use SlevomatCodingStandard\Sniffs\TestCase;

class DisallowMultiConstantDefinitionSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowMultiConstantDefinitionNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowMultiConstantDefinitionErrors.php');

		self::assertSame(6, $report->getErrorCount());

		self::assertSniffError($report, 6, DisallowMultiConstantDefinitionSniff::CODE_DISALLOWED_MULTI_CONSTANT_DEFINITION);
		self::assertSniffError($report, 8, DisallowMultiConstantDefinitionSniff::CODE_DISALLOWED_MULTI_CONSTANT_DEFINITION);
		self::assertSniffError($report, 11, DisallowMultiConstantDefinitionSniff::CODE_DISALLOWED_MULTI_CONSTANT_DEFINITION);
		self::assertSniffError($report, 13, DisallowMultiConstantDefinitionSniff::CODE_DISALLOWED_MULTI_CONSTANT_DEFINITION);
		self::assertSniffError($report, 24, DisallowMultiConstantDefinitionSniff::CODE_DISALLOWED_MULTI_CONSTANT_DEFINITION);
		self::assertSniffError($report, 26, DisallowMultiConstantDefinitionSniff::CODE_DISALLOWED_MULTI_CONSTANT_DEFINITION);

		self::assertAllFixedInFile($report);
	}

}
