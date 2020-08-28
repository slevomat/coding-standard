<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use SlevomatCodingStandard\Sniffs\TestCase;

class LongTypeHintsSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/longTypeHintsNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/longTypeHintsErrors.php');

		self::assertSame(46, $report->getErrorCount());

		self::assertSniffError(
			$report,
			4,
			LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT,
			'Expected "int" but found "integer" in @param annotation.'
		);
		self::assertSniffError(
			$report,
			5,
			LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT,
			'Expected "bool" but found "boolean" in @return annotation.'
		);
		self::assertSniffError(
			$report,
			15,
			LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT,
			'Expected "int" but found "integer" in @var annotation.'
		);
		self::assertSniffError(
			$report,
			24,
			LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT,
			'Expected "bool" but found "boolean" in @var annotation.'
		);
		self::assertSniffError(
			$report,
			30,
			LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT,
			'Expected "bool" but found "boolean" in @var annotation.'
		);
		self::assertSniffError(
			$report,
			30,
			LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT,
			'Expected "int" but found "integer" in @var annotation.'
		);
		self::assertSniffError(
			$report,
			35,
			LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT,
			'Expected "bool" but found "Boolean" in @param annotation.'
		);
		self::assertSniffError(
			$report,
			37,
			LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT,
			'Expected "int" but found "Integer" in @param annotation.'
		);
		self::assertSniffError(
			$report,
			38,
			LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT,
			'Expected "int" but found "integer" in @return annotation.'
		);
		self::assertSniffError(
			$report,
			47,
			LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT,
			'Expected "bool" but found "boolean" in @var annotation.'
		);
		self::assertSniffError(
			$report,
			47,
			LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT,
			'Expected "int" but found "integer" in @var annotation.'
		);

		self::assertSniffError(
			$report,
			56,
			LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT,
			'Expected "bool" but found "boolean" in @property annotation.'
		);
		self::assertSniffError(
			$report,
			57,
			LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT,
			'Expected "int" but found "integer" in @property-read annotation.'
		);
		self::assertSniffError(
			$report,
			58,
			LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT,
			'Expected "int" but found "integer" in @property-write annotation.'
		);
		self::assertSniffError(
			$report,
			59,
			LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT,
			'Expected "bool" but found "boolean" in @method annotation.'
		);
		self::assertSniffError(
			$report,
			59,
			LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT,
			'Expected "int" but found "integer" in @method annotation.'
		);
		self::assertSniffError(
			$report,
			59,
			LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT,
			'Expected "bool" but found "boolean" in @method annotation.'
		);
		self::assertSniffError(
			$report,
			60,
			LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT,
			'Expected "int" but found "integer" in @method annotation.'
		);
		self::assertSniffError(
			$report,
			60,
			LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT,
			'Expected "bool" but found "boolean" in @method annotation.'
		);
		self::assertSniffError(
			$report,
			61,
			LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT,
			'Expected "bool" but found "boolean" in @method annotation.'
		);
		self::assertSniffError(
			$report,
			61,
			LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT,
			'Expected "bool" but found "Boolean" in @method annotation.'
		);
		self::assertSniffError(
			$report,
			61,
			LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT,
			'Expected "int" but found "integer" in @method annotation.'
		);

		self::assertSniffError(
			$report,
			69,
			LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT,
			'Expected "int" but found "integer" in @property annotation.'
		);
		self::assertSniffError(
			$report,
			69,
			LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT,
			'Expected "bool" but found "boolean" in @property annotation.'
		);
		self::assertSniffError(
			$report,
			74,
			LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT,
			'Expected "bool" but found "boolean" in @var annotation.'
		);
		self::assertSniffError(
			$report,
			74,
			LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT,
			'Expected "int" but found "integer" in @var annotation.'
		);
		self::assertSniffError(
			$report,
			77,
			LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT,
			'Expected "int" but found "integer" in @var annotation.'
		);
		self::assertSniffError(
			$report,
			77,
			LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT,
			'Expected "bool" but found "boolean" in @var annotation.'
		);
		self::assertSniffError(
			$report,
			77,
			LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT,
			'Expected "bool" but found "boolean" in @var annotation.'
		);
		self::assertSniffError(
			$report,
			77,
			LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT,
			'Expected "int" but found "integer" in @var annotation.'
		);
		self::assertSniffError(
			$report,
			80,
			LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT,
			'Expected "int" but found "integer" in @var annotation.'
		);
		self::assertSniffError(
			$report,
			83,
			LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT,
			'Expected "int" but found "integer" in @var annotation.'
		);
		self::assertSniffError(
			$report,
			86,
			LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT,
			'Expected "bool" but found "boolean" in @var annotation.'
		);
		self::assertSniffError(
			$report,
			89,
			LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT,
			'Expected "int" but found "integer" in @var annotation.'
		);
		self::assertSniffError(
			$report,
			89,
			LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT,
			'Expected "bool" but found "boolean" in @var annotation.'
		);
		self::assertSniffError(
			$report,
			92,
			LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT,
			'Expected "int" but found "integer" in @var annotation.'
		);
		self::assertSniffError(
			$report,
			92,
			LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT,
			'Expected "bool" but found "boolean" in @var annotation.'
		);
		self::assertSniffError(
			$report,
			95,
			LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT,
			'Expected "int" but found "integer" in @var annotation.'
		);
		self::assertSniffError(
			$report,
			95,
			LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT,
			'Expected "bool" but found "boolean" in @var annotation.'
		);

		self::assertSniffError(
			$report,
			104,
			LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT,
			'Expected "bool" but found "boolean" in @return annotation.'
		);
		self::assertSniffError(
			$report,
			104,
			LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT,
			'Expected "int" but found "integer" in @return annotation.'
		);

		self::assertSniffError(
			$report,
			114,
			LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT,
			'Expected "bool" but found "boolean" in @return annotation.'
		);

		self::assertSniffError(
			$report,
			121,
			LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT,
			'Expected "int" but found "integer" in @var annotation.'
		);
		self::assertSniffError(
			$report,
			121,
			LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT,
			'Expected "bool" but found "boolean" in @var annotation.'
		);
		self::assertSniffError(
			$report,
			124,
			LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT,
			'Expected "int" but found "integer" in @var annotation.'
		);
		self::assertSniffError(
			$report,
			124,
			LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT,
			'Expected "bool" but found "boolean" in @var annotation.'
		);

		self::assertAllFixedInFile($report);
	}

}
