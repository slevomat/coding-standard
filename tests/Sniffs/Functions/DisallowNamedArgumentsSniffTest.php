<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Functions;

use SlevomatCodingStandard\Sniffs\TestCase;

class DisallowNamedArgumentsSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowNamedArgumentsNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowNamedArgumentsErrors.php');

		self::assertSame(6, $report->getErrorCount());

		self::assertSniffError(
			$report,
			3,
			DisallowNamedArgumentsSniff::CODE_DISALLOWED_NAMED_ARGUMENT,
			'Named arguments are disallowed, usage of named argument "a" found.'
		);
		self::assertSniffError(
			$report,
			3,
			DisallowNamedArgumentsSniff::CODE_DISALLOWED_NAMED_ARGUMENT,
			'Named arguments are disallowed, usage of named argument "b" found.'
		);
		self::assertSniffError(
			$report,
			6,
			DisallowNamedArgumentsSniff::CODE_DISALLOWED_NAMED_ARGUMENT,
			'Named arguments are disallowed, usage of named argument "a" found.'
		);
		self::assertSniffError(
			$report,
			7,
			DisallowNamedArgumentsSniff::CODE_DISALLOWED_NAMED_ARGUMENT,
			'Named arguments are disallowed, usage of named argument "b" found.'
		);
		self::assertSniffError(
			$report,
			12,
			DisallowNamedArgumentsSniff::CODE_DISALLOWED_NAMED_ARGUMENT,
			'Named arguments are disallowed, usage of named argument "a" found.'
		);
		self::assertSniffError(
			$report,
			12,
			DisallowNamedArgumentsSniff::CODE_DISALLOWED_NAMED_ARGUMENT,
			'Named arguments are disallowed, usage of named argument "b" found.'
		);
	}

}
