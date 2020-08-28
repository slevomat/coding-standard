<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use SlevomatCodingStandard\Sniffs\TestCase;

class RequireOneNamespaceInFileSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireOneNamespaceInFileNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireOneNamespaceInFileErrors.php');

		self::assertSame(2, $report->getErrorCount());

		self::assertSniffError($report, 10, RequireOneNamespaceInFileSniff::CODE_MORE_NAMESPACES_IN_FILE);
		self::assertSniffError($report, 18, RequireOneNamespaceInFileSniff::CODE_MORE_NAMESPACES_IN_FILE);
	}

	public function testNoNamespaceNoError(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireOneNamespaceInFileNoNamespaceNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

}
