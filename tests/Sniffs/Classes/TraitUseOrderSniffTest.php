<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use SlevomatCodingStandard\Sniffs\TestCase;

class TraitUseOrderSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/traitUseOrderNoErrors.php');
		self::assertNoSniffErrorInFile($report);
		$report = self::checkFile(__DIR__ . '/data/traitUseEnumOrderNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/traitUseOrderErrors.php');

		self::assertSame(7, $report->getErrorCount());

		self::assertSniffError($report, 3, TraitUseOrderSniff::CODE_INCORRECT_ORDER);
		self::assertSniffError($report, 12, TraitUseOrderSniff::CODE_INCORRECT_ORDER);
		self::assertSniffError($report, 21, TraitUseOrderSniff::CODE_INCORRECT_ORDER);
		self::assertSniffError($report, 32, TraitUseOrderSniff::CODE_INCORRECT_ORDER);
		self::assertSniffError($report, 39, TraitUseOrderSniff::CODE_INCORRECT_ORDER);
		self::assertSniffError($report, 47, TraitUseOrderSniff::CODE_INCORRECT_ORDER);
		self::assertSniffError($report, 57, TraitUseOrderSniff::CODE_INCORRECT_ORDER);

		self::assertAllFixedInFile($report);
	}

	public function testCaseSensitiveNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/traitUseOrderCaseSensitiveNoErrors.php', [
			'caseSensitive' => true,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testCaseSensitiveErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/traitUseOrderCaseSensitiveErrors.php', [
			'caseSensitive' => true,
		]);

		self::assertSame(1, $report->getErrorCount());

		self::assertSniffError($report, 3, TraitUseOrderSniff::CODE_INCORRECT_ORDER);

		self::assertAllFixedInFile($report);
	}

}
