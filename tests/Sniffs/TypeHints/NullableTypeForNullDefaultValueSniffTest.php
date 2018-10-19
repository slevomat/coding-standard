<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use SlevomatCodingStandard\Sniffs\TestCase;

class NullableTypeForNullDefaultValueSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		self::assertNoSniffErrorInFile(self::checkFile(__DIR__ . '/data/nullableTypeForNullDefaultValueNoErrors.php', [
			'enabled' => true,
		]));
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/nullableTypeForNullDefaultValueErrors.php', [
			'enabled' => true,
		]);

		self::assertSame(12, $report->getErrorCount());

		$code = NullableTypeForNullDefaultValueSniff::CODE_NULLABILITY_SYMBOL_REQUIRED;
		self::assertSniffError($report, 3, $code);
		self::assertSniffError($report, 8, $code);
		self::assertSniffError($report, 15, $code);
		self::assertSniffError($report, 22, $code);
		self::assertSniffError($report, 27, $code);
		self::assertSniffError($report, 32, $code);
		self::assertSniffError($report, 37, $code);
		self::assertSniffError($report, 42, $code);
		self::assertSniffError($report, 47, $code);
		self::assertSniffError($report, 52, $code);
		self::assertSniffError($report, 57, $code);
		self::assertSniffError($report, 62, $code);
	}

	public function testFixable(): void
	{
		$codes = [NullableTypeForNullDefaultValueSniff::CODE_NULLABILITY_SYMBOL_REQUIRED];
		$report = self::checkFile(__DIR__ . '/data/fixableNullableTypeForNullDefaultValue.php', [
			'enabled' => true,
		], $codes);
		self::assertAllFixedInFile($report);
	}

	public function testDisabledSniff(): void
	{
		$report = self::checkFile(__DIR__ . '/data/nullableTypeForNullDefaultValueErrors.php', [
			'enabled' => false,
		]);
		self::assertNoSniffErrorInFile($report);
	}

}
