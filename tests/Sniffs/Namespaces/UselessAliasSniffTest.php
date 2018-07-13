<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use SlevomatCodingStandard\Sniffs\TestCase;

class UselessAliasSniffTest extends TestCase
{

	public function testNoUses(): void
	{
		$report = self::checkFile(__DIR__ . '/data/uselessAliasNoUses.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/uselessAliasNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/uselessAliasErrors.php');

		self::assertSame(3, $report->getErrorCount());

		self::assertSniffError($report, 3, UselessAliasSniff::CODE_USELESS_ALIAS);
		self::assertSniffError($report, 4, UselessAliasSniff::CODE_USELESS_ALIAS);
		self::assertSniffError($report, 6, UselessAliasSniff::CODE_USELESS_ALIAS);

		self::assertAllFixedInFile($report);
	}

}
