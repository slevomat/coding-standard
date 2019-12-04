<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Functions;

use SlevomatCodingStandard\Sniffs\TestCase;

class RequireArrowFunctionSniffTest extends TestCase
{

	public function testDisallowNestedNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireArrowFunctionDisallowNestedNoErrors.php', [
			'allowNested' => false,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testDisallowNestedErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireArrowFunctionDisallowNestedErrors.php', [
			'allowNested' => false,
		]);

		self::assertSame(3, $report->getErrorCount());

		self::assertSniffError($report, 3, RequireArrowFunctionSniff::CODE_REQUIRED_ARROW_FUNCTION);
		self::assertSniffError($report, 7, RequireArrowFunctionSniff::CODE_REQUIRED_ARROW_FUNCTION);
		self::assertSniffError($report, 12, RequireArrowFunctionSniff::CODE_REQUIRED_ARROW_FUNCTION);

		self::assertAllFixedInFile($report);
	}

	public function testAllowNestedNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireArrowFunctionAllowNestedNoErrors.php', [
			'allowNested' => true,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testAllowNestedErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireArrowFunctionAllowNestedErrors.php', [
			'allowNested' => true,
		]);

		self::assertSame(3, $report->getErrorCount());

		self::assertSniffError($report, 3, RequireArrowFunctionSniff::CODE_REQUIRED_ARROW_FUNCTION);
		self::assertSniffError($report, 4, RequireArrowFunctionSniff::CODE_REQUIRED_ARROW_FUNCTION);
		self::assertSniffError($report, 4, RequireArrowFunctionSniff::CODE_REQUIRED_ARROW_FUNCTION);

		self::assertAllFixedInFile($report);
	}

}
