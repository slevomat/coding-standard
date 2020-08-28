<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\PHP;

use SlevomatCodingStandard\Sniffs\TestCase;

class TypeCastSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/typeCastNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/typeCastErrors.php');

		self::assertSame(8, $report->getErrorCount());

		self::assertSniffError(
			$report,
			3,
			TypeCastSniff::CODE_FORBIDDEN_CAST_USED,
			'Cast "(unset)" is forbidden, use "unset(...)" or assign "null" instead.'
		);
		self::assertSniffError($report, 4, TypeCastSniff::CODE_FORBIDDEN_CAST_USED, 'Cast "(binary)" is forbidden and has no effect.');

		self::assertSniffError($report, 6, TypeCastSniff::CODE_INVALID_CAST_USED, 'Cast "(boolean)" is forbidden, use "(bool)" instead.');
		self::assertSniffError($report, 7, TypeCastSniff::CODE_INVALID_CAST_USED, 'Cast "(double)" is forbidden, use "(float)" instead.');
		self::assertSniffError($report, 8, TypeCastSniff::CODE_INVALID_CAST_USED, 'Cast "(integer)" is forbidden, use "(int)" instead.');
		self::assertSniffError($report, 9, TypeCastSniff::CODE_INVALID_CAST_USED, 'Cast "(real)" is forbidden, use "(float)" instead.');

		self::assertSniffError($report, 11, TypeCastSniff::CODE_INVALID_CAST_USED, 'Cast "(DOUBLE)" is forbidden, use "(float)" instead.');
		self::assertSniffError($report, 12, TypeCastSniff::CODE_INVALID_CAST_USED, 'Cast "( integer )" is forbidden, use "(int)" instead.');

		self::assertAllFixedInFile($report);
	}

}
