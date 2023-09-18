<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Arrays;

use SlevomatCodingStandard\Sniffs\TestCase;

class DisallowImplicitArrayCreationSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowImplicitArrayCreationNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowImplicitArrayCreationErrors.php');

		self::assertSame(9, $report->getErrorCount());

		self::assertSniffError($report, 3, DisallowImplicitArrayCreationSniff::CODE_IMPLICIT_ARRAY_CREATION_USED);
		self::assertSniffError($report, 7, DisallowImplicitArrayCreationSniff::CODE_IMPLICIT_ARRAY_CREATION_USED);
		self::assertSniffError($report, 13, DisallowImplicitArrayCreationSniff::CODE_IMPLICIT_ARRAY_CREATION_USED);
		self::assertSniffError($report, 20, DisallowImplicitArrayCreationSniff::CODE_IMPLICIT_ARRAY_CREATION_USED);
		self::assertSniffError($report, 30, DisallowImplicitArrayCreationSniff::CODE_IMPLICIT_ARRAY_CREATION_USED);
		self::assertSniffError($report, 36, DisallowImplicitArrayCreationSniff::CODE_IMPLICIT_ARRAY_CREATION_USED);
		self::assertSniffError($report, 41, DisallowImplicitArrayCreationSniff::CODE_IMPLICIT_ARRAY_CREATION_USED);
		self::assertSniffError($report, 48, DisallowImplicitArrayCreationSniff::CODE_IMPLICIT_ARRAY_CREATION_USED);
		self::assertSniffError($report, 54, DisallowImplicitArrayCreationSniff::CODE_IMPLICIT_ARRAY_CREATION_USED);
	}

}
