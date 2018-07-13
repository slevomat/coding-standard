<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\PHP;

use SlevomatCodingStandard\Sniffs\TestCase;

class UselessSemicolonSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/uselessSemicolonNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/uselessSemicolonErrors.php');

		self::assertSame(12, $report->getErrorCount());

		self::assertSniffError($report, 3, UselessSemicolonSniff::CODE_USELESS_SEMICOLON);
		self::assertSniffError($report, 5, UselessSemicolonSniff::CODE_USELESS_SEMICOLON);
		self::assertSniffError($report, 6, UselessSemicolonSniff::CODE_USELESS_SEMICOLON);
		self::assertSniffError($report, 8, UselessSemicolonSniff::CODE_USELESS_SEMICOLON);
		self::assertSniffError($report, 12, UselessSemicolonSniff::CODE_USELESS_SEMICOLON);
		self::assertSniffError($report, 16, UselessSemicolonSniff::CODE_USELESS_SEMICOLON);
		self::assertSniffError($report, 23, UselessSemicolonSniff::CODE_USELESS_SEMICOLON);
		self::assertSniffError($report, 28, UselessSemicolonSniff::CODE_USELESS_SEMICOLON);
		self::assertSniffError($report, 33, UselessSemicolonSniff::CODE_USELESS_SEMICOLON);
		self::assertSniffError($report, 35, UselessSemicolonSniff::CODE_USELESS_SEMICOLON);

		self::assertAllFixedInFile($report);
	}

}
