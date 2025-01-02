<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use SlevomatCodingStandard\Sniffs\TestCase;

class MissingNativeConstantTypeSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/missingConstantTypeHintNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/missingConstantTypeHintErrors.php');

		self::assertSame(36, $report->getErrorCount());

		for ($i = 8; $i < 16; $i++) {
			self::assertSniffError($report, $i, MissingNativeConstantTypeSniff::CODE_MISSING_CONSTANT_TYPE);
		}

		for ($i = 23; $i < 31; $i++) {
			self::assertSniffError($report, $i, MissingNativeConstantTypeSniff::CODE_MISSING_CONSTANT_TYPE);
		}

		for ($i = 38; $i < 46; $i++) {
			self::assertSniffError($report, $i, MissingNativeConstantTypeSniff::CODE_MISSING_CONSTANT_TYPE);
		}

		for ($i = 53; $i < 61; $i++) {
			self::assertSniffError($report, $i, MissingNativeConstantTypeSniff::CODE_MISSING_CONSTANT_TYPE);
		}

		self::assertAllFixedInFile($report);
	}

	public function testIgnoredBySuppress(): void
	{
		$report = self::checkFile(__DIR__ . '/data/missingConstantTypeHintIgnoreErrors.php');

		self::assertSame(0, $report->getErrorCount());
	}

	public function testWithEnableConfigEnabled(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/missingConstantTypeHintErrors.php',
			['enable' => true],
			[MissingNativeConstantTypeSniff::CODE_MISSING_CONSTANT_TYPE]
		);
		self::assertAllFixedInFile($report);
	}

	public function testWithEnableConfigDisabled(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/missingConstantTypeHintDisabled.php',
			['enable' => false],
			[MissingNativeConstantTypeSniff::CODE_MISSING_CONSTANT_TYPE]
		);
		self::assertAllFixedInFile($report);
	}

}
