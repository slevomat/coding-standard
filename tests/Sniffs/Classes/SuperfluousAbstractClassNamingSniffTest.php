<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use SlevomatCodingStandard\Sniffs\TestCase;

class SuperfluousAbstractClassNamingSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/superfluousAbstractClassNamingNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/superfluousAbstractClassNamingErrors.php');

		self::assertSame(6, $report->getErrorCount());

		self::assertSniffError($report, 3, SuperfluousAbstractClassNamingSniff::CODE_SUPERFLUOUS_PREFIX, 'Superfluous prefix "Abstract".');
		self::assertSniffError($report, 8, SuperfluousAbstractClassNamingSniff::CODE_SUPERFLUOUS_PREFIX, 'Superfluous prefix "abstract".');
		self::assertSniffError($report, 13, SuperfluousAbstractClassNamingSniff::CODE_SUPERFLUOUS_PREFIX, 'Superfluous prefix "AbStRaCt".');

		self::assertSniffError($report, 18, SuperfluousAbstractClassNamingSniff::CODE_SUPERFLUOUS_SUFFIX, 'Superfluous suffix "Abstract".');
		self::assertSniffError($report, 23, SuperfluousAbstractClassNamingSniff::CODE_SUPERFLUOUS_SUFFIX, 'Superfluous suffix "abstract".');
		self::assertSniffError($report, 28, SuperfluousAbstractClassNamingSniff::CODE_SUPERFLUOUS_SUFFIX, 'Superfluous suffix "AbStRaCt".');
	}

}
