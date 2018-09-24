<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use SlevomatCodingStandard\Sniffs\TestCase;

class SuperfluousTraitNamingSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/superfluousTraitNamingNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/superfluousTraitNamingErrors.php');

		self::assertSame(3, $report->getErrorCount());

		self::assertSniffError($report, 3, SuperfluousTraitNamingSniff::CODE_SUPERFLUOUS_SUFFIX, 'Superfluous suffix "Trait".');
		self::assertSniffError($report, 8, SuperfluousTraitNamingSniff::CODE_SUPERFLUOUS_SUFFIX, 'Superfluous suffix "trait".');
		self::assertSniffError($report, 13, SuperfluousTraitNamingSniff::CODE_SUPERFLUOUS_SUFFIX, 'Superfluous suffix "TrAiT".');
	}

}
