<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use SlevomatCodingStandard\Sniffs\TestCase;
use function range;

class DisallowYodaComparisonSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowYodaComparisonNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowYodaComparisonErrors.php');
		foreach (range(3, 37) as $lineNumber) {
			self::assertSniffError($report, $lineNumber, DisallowYodaComparisonSniff::CODE_DISALLOWED_YODA_COMPARISON);
		}
	}

	public function testFixable(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/fixableDisallowYodaComparisons.php',
			[],
			[DisallowYodaComparisonSniff::CODE_DISALLOWED_YODA_COMPARISON]
		);
		self::assertAllFixedInFile($report);
	}

}
