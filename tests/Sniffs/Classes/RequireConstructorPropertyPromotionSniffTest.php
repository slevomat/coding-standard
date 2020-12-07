<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use SlevomatCodingStandard\Sniffs\TestCase;

class RequireConstructorPropertyPromotionSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireConstructorPropertyPromotionNoErrors.php', [
			'enable' => true,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireConstructorPropertyPromotionErrors.php', [
			'enable' => true,
		]);

		self::assertSame(3, $report->getErrorCount());

		self::assertSniffError(
			$report,
			6,
			RequireConstructorPropertyPromotionSniff::CODE_REQUIRED_CONSTRUCTOR_PROPERTY_PROMOTION,
			'Required promotion of property $a.'
		);
		self::assertSniffError(
			$report,
			13,
			RequireConstructorPropertyPromotionSniff::CODE_REQUIRED_CONSTRUCTOR_PROPERTY_PROMOTION,
			'Required promotion of property $b.'
		);
		self::assertSniffError(
			$report,
			15,
			RequireConstructorPropertyPromotionSniff::CODE_REQUIRED_CONSTRUCTOR_PROPERTY_PROMOTION,
			'Required promotion of property $c.'
		);

		self::assertAllFixedInFile($report);
	}

	public function testShouldNotReportIfSniffIsDisabled(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireConstructorPropertyPromotionErrors.php', [
			'enable' => false,
		]);

		self::assertNoSniffErrorInFile($report);
	}

}
