<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use SlevomatCodingStandard\Sniffs\TestCase;

class DisallowTrailingCommaInMatchExpressionSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowTrailingCommaInMatchExpressionNoErrors.php', [
			'onlySingleLine' => false,
		]);

		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowTrailingCommaInMatchExpressionErrors.php', [
			'onlySingleLine' => false,
		]);

		self::assertSame(7, $report->getErrorCount());

		self::assertSniffError($report, 5, DisallowTrailingCommaInMatchExpressionSniff::CODE_DISALLOWED_TRAILING_COMMA);
		self::assertSniffError($report, 9, DisallowTrailingCommaInMatchExpressionSniff::CODE_DISALLOWED_TRAILING_COMMA);
		self::assertSniffError($report, 16, DisallowTrailingCommaInMatchExpressionSniff::CODE_DISALLOWED_TRAILING_COMMA);
		self::assertSniffError($report, 17, DisallowTrailingCommaInMatchExpressionSniff::CODE_DISALLOWED_TRAILING_COMMA);
		self::assertSniffError($report, 24, DisallowTrailingCommaInMatchExpressionSniff::CODE_DISALLOWED_TRAILING_COMMA);
		self::assertSniffError($report, 34, DisallowTrailingCommaInMatchExpressionSniff::CODE_DISALLOWED_TRAILING_COMMA);
		self::assertSniffError($report, 35, DisallowTrailingCommaInMatchExpressionSniff::CODE_DISALLOWED_TRAILING_COMMA);

		self::assertAllFixedInFile($report);
	}

	public function testWithOnlySingleLineEnabledErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowTrailingCommaInMatchExpressionWithOnlySingleLineEnabledErrors.php', [
			'onlySingleLine' => true,
		]);

		self::assertSame(1, $report->getErrorCount());
		self::assertSniffError($report, 5, DisallowTrailingCommaInMatchExpressionSniff::CODE_DISALLOWED_TRAILING_COMMA);
		self::assertAllFixedInFile($report);
	}

}
