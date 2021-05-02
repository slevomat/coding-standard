<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use SlevomatCodingStandard\Sniffs\TestCase;

class MultipleUsesPerLineSniffTest extends TestCase
{

	public function testMultipleUsesPerLine(): void
	{
		$report = self::checkFile(__DIR__ . '/data/multipleUsesPerLine.php');

		self::assertSniffError($report, 5, MultipleUsesPerLineSniff::CODE_MULTIPLE_USES_PER_LINE);

		// Ignore commas in closure use
		self::assertNoSniffError($report, 7);
	}

}
