<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Functions;

use SlevomatCodingStandard\Sniffs\TestCase;

class UnusedInheritedVariablePassedToClosureSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/unusedInheritedVariablePassedToClosureNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/unusedInheritedVariablePassedToClosureErrors.php');

		self::assertSame(6, $report->getErrorCount());

		self::assertSniffError(
			$report,
			3,
			UnusedInheritedVariablePassedToClosureSniff::CODE_UNUSED_INHERITED_VARIABLE,
			'Unused inherited variable $boo passed to closure.'
		);
		self::assertSniffError(
			$report,
			10,
			UnusedInheritedVariablePassedToClosureSniff::CODE_UNUSED_INHERITED_VARIABLE,
			'Unused inherited variable $foo passed to closure.'
		);
		self::assertSniffError(
			$report,
			17,
			UnusedInheritedVariablePassedToClosureSniff::CODE_UNUSED_INHERITED_VARIABLE,
			'Unused inherited variable $doo passed to closure.'
		);
		self::assertSniffError(
			$report,
			23,
			UnusedInheritedVariablePassedToClosureSniff::CODE_UNUSED_INHERITED_VARIABLE,
			'Unused inherited variable $boo passed to closure.'
		);
		self::assertSniffError(
			$report,
			30,
			UnusedInheritedVariablePassedToClosureSniff::CODE_UNUSED_INHERITED_VARIABLE,
			'Unused inherited variable $boo passed to closure.'
		);
		self::assertSniffError(
			$report,
			38,
			UnusedInheritedVariablePassedToClosureSniff::CODE_UNUSED_INHERITED_VARIABLE,
			'Unused inherited variable $output passed to closure.'
		);

		self::assertAllFixedInFile($report);
	}

}
