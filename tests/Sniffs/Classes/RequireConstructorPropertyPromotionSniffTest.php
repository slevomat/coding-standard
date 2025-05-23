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

		self::assertSame(9, $report->getErrorCount());

		self::assertSniffError(
			$report,
			6,
			RequireConstructorPropertyPromotionSniff::CODE_REQUIRED_CONSTRUCTOR_PROPERTY_PROMOTION,
			'Required promotion of property $a.',
		);
		self::assertSniffError(
			$report,
			11,
			RequireConstructorPropertyPromotionSniff::CODE_REQUIRED_CONSTRUCTOR_PROPERTY_PROMOTION,
			'Required promotion of property $b.',
		);
		self::assertSniffError(
			$report,
			13,
			RequireConstructorPropertyPromotionSniff::CODE_REQUIRED_CONSTRUCTOR_PROPERTY_PROMOTION,
			'Required promotion of property $c.',
		);
		self::assertSniffError(
			$report,
			18,
			RequireConstructorPropertyPromotionSniff::CODE_REQUIRED_CONSTRUCTOR_PROPERTY_PROMOTION,
			'Required promotion of property $e.',
		);
		self::assertSniffError(
			$report,
			20,
			RequireConstructorPropertyPromotionSniff::CODE_REQUIRED_CONSTRUCTOR_PROPERTY_PROMOTION,
			'Required promotion of property $f.',
		);
		self::assertSniffError(
			$report,
			23,
			RequireConstructorPropertyPromotionSniff::CODE_REQUIRED_CONSTRUCTOR_PROPERTY_PROMOTION,
			'Required promotion of property $g.',
		);
		self::assertSniffError(
			$report,
			44,
			RequireConstructorPropertyPromotionSniff::CODE_REQUIRED_CONSTRUCTOR_PROPERTY_PROMOTION,
			'Required promotion of property $from.',
		);
		self::assertSniffError(
			$report,
			60,
			RequireConstructorPropertyPromotionSniff::CODE_REQUIRED_CONSTRUCTOR_PROPERTY_PROMOTION,
			'Required promotion of property $login.',
		);
		self::assertSniffError(
			$report,
			61,
			RequireConstructorPropertyPromotionSniff::CODE_REQUIRED_CONSTRUCTOR_PROPERTY_PROMOTION,
			'Required promotion of property $password.',
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
