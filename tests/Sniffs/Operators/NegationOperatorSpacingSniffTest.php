<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Operators;

use SlevomatCodingStandard\Sniffs\TestCase;

class NegationOperatorSpacingSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/negationOperatorSpacingNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/negationOperatorSpacingErrors.php');

		self::assertSame(95, $report->getErrorCount());

		self::assertAllFixedInFile($report);
	}

	public function testRequireSpaceNoErrors(): void
	{
		self::assertNoSniffErrorInFile(
			self::checkFile(__DIR__ . '/data/negationOperatorSpacingRequireSpaceNoErrors.php', ['spacesCount' => 1])
		);
	}

	public function testRequireSpaceErrors(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/negationOperatorSpacingRequireSpaceErrors.php',
			['spacesCount' => 1]
		);

		self::assertSame(95, $report->getErrorCount());

		self::assertAllFixedInFile($report);
	}

}
