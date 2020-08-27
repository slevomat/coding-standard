<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Functions;

use SlevomatCodingStandard\Sniffs\TestCase;

class UselessParameterDefaultValueSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/uselessParameterDefaultValueNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/uselessParameterDefaultValueErrors.php');

		self::assertSame(5, $report->getErrorCount());

		self::assertSniffError(
			$report,
			3,
			UselessParameterDefaultValueSniff::CODE_USELESS_PARAMETER_DEFAULT_VALUE,
			'Useless default value of parameter $a.'
		);
		self::assertSniffError(
			$report,
			10,
			UselessParameterDefaultValueSniff::CODE_USELESS_PARAMETER_DEFAULT_VALUE,
			'Useless default value of parameter $bb.'
		);
		self::assertSniffError(
			$report,
			11,
			UselessParameterDefaultValueSniff::CODE_USELESS_PARAMETER_DEFAULT_VALUE,
			'Useless default value of parameter $bbb.'
		);
		self::assertSniffError(
			$report,
			17,
			UselessParameterDefaultValueSniff::CODE_USELESS_PARAMETER_DEFAULT_VALUE,
			'Useless default value of parameter $c.'
		);
		self::assertSniffError(
			$report,
			22,
			UselessParameterDefaultValueSniff::CODE_USELESS_PARAMETER_DEFAULT_VALUE,
			'Useless default value of parameter $d.'
		);

		self::assertAllFixedInFile($report);
	}

}
