<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

class RequireOneNamespaceInFileSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testNoErrors(): void
	{
		self::assertNoSniffErrorInFile(self::checkFile(__DIR__ . '/data/requireOneNamespaceInFileNoErrors.php'));
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireOneNamespaceInFileErrors.php');

		self::assertSame(2, $report->getErrorCount());

		self::assertSniffError($report, 8, RequireOneNamespaceInFileSniff::CODE_MORE_NAMESPACES_IN_FILE);
		self::assertSniffError($report, 13, RequireOneNamespaceInFileSniff::CODE_MORE_NAMESPACES_IN_FILE);
	}

	public function testNoNamespaceNoError(): void
	{
		self::assertNoSniffErrorInFile(self::checkFile(__DIR__ . '/data/requireOneNamespaceInFileNoNamespaceNoErrors.php'));
	}

}
