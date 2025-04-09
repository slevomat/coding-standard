<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use SlevomatCodingStandard\Sniffs\TestCase;

class RequireNullCoalesceEqualOperatorSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireNullCoalesceEqualOperatorNoErrors.php', [
			'enable' => true,
			'checkIfConditions' => true,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/requireNullCoalesceEqualOperatorErrors.php',
			['enable' => true, 'checkIfConditions' => true],
			[RequireNullCoalesceEqualOperatorSniff::CODE_REQUIRED_NULL_COALESCE_EQUAL_OPERATOR],
		);

		self::assertSame(14, $report->getErrorCount());

		foreach ([3, 5, 7, 9, 10, 12, 14, 15, 17, 21, 23] as $line) {
			self::assertSniffError($report, $line, RequireNullCoalesceEqualOperatorSniff::CODE_REQUIRED_NULL_COALESCE_EQUAL_OPERATOR);
		}

		foreach ([25, 30, 35] as $line) {
			self::assertSniffError($report, $line, RequireNullCoalesceEqualOperatorSniff::CODE_REQUIRED_NULL_COALESCE_EQUAL_OPERATOR);
		}

		self::assertAllFixedInFile($report);
	}

	public function testShouldNotReportIfSniffIsDisabled(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/requireNullCoalesceEqualOperatorErrors.php',
			['enable' => false],
			[RequireNullCoalesceEqualOperatorSniff::CODE_REQUIRED_NULL_COALESCE_EQUAL_OPERATOR],
		);

		self::assertNoSniffErrorInFile($report);
	}

	public function testShouldNotReportIfCheckIfConditionIsDisabled(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/requireNullCoalesceEqualOperatorCheckIfConditionDisabled.php',
			['enable' => true, 'checkIfConditions' => false],
			[RequireNullCoalesceEqualOperatorSniff::CODE_REQUIRED_NULL_COALESCE_EQUAL_OPERATOR],
		);

		self::assertNoSniffErrorInFile($report);
	}

}
