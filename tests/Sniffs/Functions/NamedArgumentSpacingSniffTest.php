<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Functions;

use SlevomatCodingStandard\Sniffs\TestCase;

class NamedArgumentSpacingSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/namedArgumentSpacingNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/namedArgumentSpacingErrors.php');

		self::assertSame(3, $report->getErrorCount());

		self::assertSniffError(
			$report,
			3,
			NamedArgumentSpacingSniff::CODE_WHITESPACE_BEFORE_COLOR,
			'There must be no whitespace between named argument "search" and colon.'
		);
		self::assertSniffError(
			$report,
			4,
			NamedArgumentSpacingSniff::CODE_NO_WHITESPACE_AFTER_COLON,
			'There must be exactly one space after colon in named argument "search".'
		);
		self::assertSniffError(
			$report,
			4,
			NamedArgumentSpacingSniff::CODE_NO_WHITESPACE_AFTER_COLON,
			'There must be exactly one space after colon in named argument "subject".'
		);

		self::assertAllFixedInFile($report);
	}

}
