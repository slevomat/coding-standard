<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use SlevomatCodingStandard\Sniffs\TestCase;

class TraitUseDeclarationSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/traitUseDeclarationNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/traitUseDeclarationErrors.php');

		self::assertSame(3, $report->getErrorCount());

		self::assertSniffError($report, 6, TraitUseDeclarationSniff::CODE_MULTIPLE_TRAITS_PER_DECLARATION);
		self::assertSniffError($report, 8, TraitUseDeclarationSniff::CODE_MULTIPLE_TRAITS_PER_DECLARATION);
		self::assertSniffError($report, 10, TraitUseDeclarationSniff::CODE_MULTIPLE_TRAITS_PER_DECLARATION);

		self::assertAllFixedInFile($report);
	}

}
