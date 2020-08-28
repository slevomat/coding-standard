<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use SlevomatCodingStandard\Sniffs\TestCase;

class NullableTypeForNullDefaultValueSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/nullableTypeForNullDefaultValueNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/nullableTypeForNullDefaultValueErrors.php');

		self::assertSame(13, $report->getErrorCount());

		$code = NullableTypeForNullDefaultValueSniff::CODE_NULLABILITY_SYMBOL_REQUIRED;
		self::assertSniffError($report, 3, $code);
		self::assertSniffError($report, 8, $code);
		self::assertSniffError($report, 12, $code);
		self::assertSniffError($report, 17, $code);
		self::assertSniffError($report, 24, $code);
		self::assertSniffError($report, 29, $code);
		self::assertSniffError($report, 34, $code);
		self::assertSniffError($report, 39, $code);
		self::assertSniffError($report, 44, $code);
		self::assertSniffError($report, 49, $code);
		self::assertSniffError($report, 54, $code);
		self::assertSniffError($report, 59, $code);
		self::assertSniffError($report, 64, $code);
	}

	public function testFixable(): void
	{
		$codes = [NullableTypeForNullDefaultValueSniff::CODE_NULLABILITY_SYMBOL_REQUIRED];
		$report = self::checkFile(__DIR__ . '/data/fixableNullableTypeForNullDefaultValue.php', [], $codes);
		self::assertAllFixedInFile($report);
	}

}
